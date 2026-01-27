import axios from "axios";
import { authService } from './auth.service';

const API_BASE_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000/api";

export const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
    },
});

// Request interceptor
apiClient.interceptors.request.use(
    (config) => {
        const token = authService.getToken();
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

// Response interceptor
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // إذا كان الخطأ 401 ولم نكن قد حاولنا تجديد التوكن بعد
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                const refreshToken = authService.getRefreshToken();
                if (refreshToken) {
                    const response = await authService.refreshToken(refreshToken);

                    // حفظ التوكن الجديد
                    if (response.data?.access_token) {
                        const user = authService.getCurrentUser();
                        const userType = authService.getUserType();

                        if (user && userType) {
                            authService.saveAuthData({
                                token: response.data.access_token,
                                refreshToken: response.data.refresh_token,
                                user: response.data.user || user,
                                userType
                            });
                        }

                        // إعادة المحاولة مع التوكن الجديد
                        originalRequest.headers.Authorization = `Bearer ${response.data.access_token}`;
                        return apiClient(originalRequest);
                    }
                }
            } catch (refreshError) {
                // إذا فشل تجديد التوكن، نقوم بتسجيل الخروج
                await authService.logout();
                window.location.href = '/login';
                return Promise.reject(refreshError);
            }
        }

        return Promise.reject(error);
    }
);
