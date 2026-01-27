import { apiClient } from "../api/axios";
interface User {
    id: number;
    name: string;
    email: string;
    type: "admin" | "client" | "owner" | string;
    status: string;
    phone?: string | null;
    company_id?: number | null;
    created_at: string;
    updated_at: string;
}

interface LoginCredentials {
    email: string;
    password: string;
}

export const authService = {
    // تسجيل الدخول للادمن
    async adminLogin(credentials: LoginCredentials): Promise<AuthResponse> {
        const response = await apiClient.post("/admin/login", credentials);
        return response.data;
    },

    // تسجيل الدخول للعميل
    async clientLogin(credentials: LoginCredentials): Promise<AuthResponse> {
        const response = await apiClient.post("/client/login", credentials);
        return response.data;
    },

    // تسجيل الخروج
    async logout(): Promise<void> {
        // إزالة التوكن من التخزين
        localStorage.removeItem("auth_token");
        localStorage.removeItem("refresh_token");
        localStorage.removeItem("user_type");
        localStorage.removeItem("user_data");
    },

    // تجديد التوكن
    async refreshToken(refreshToken: string): Promise<AuthResponse> {
        const response = await apiClient.post("/auth/refresh", {
            refresh_token: refreshToken,
        });
        return response.data;
    },

    // الحصول على بيانات المستخدم الحالي
    getCurrentUser(): User | null {
        const userStr = localStorage.getItem("user_data");
        if (!userStr) return null;
        return JSON.parse(userStr);
    },

    // الحصول على التوكن
    getToken(): string | null {
        return localStorage.getItem("auth_token");
    },

    // الحصول على التوكن الخاص بالتجديد
    getRefreshToken(): string | null {
        return localStorage.getItem("refresh_token");
    },

    // الحصول على نوع المستخدم
    getUserType(): "admin" | "client" | null {
        return localStorage.getItem("user_type") as "admin" | "client" | null;
    },

    // حفظ بيانات المصادقة
    saveAuthData(data: {
        token: string;
        refreshToken: string;
        user: User;
        userType: "admin" | "client";
    }): void {
        localStorage.setItem("auth_token", data.token);
        localStorage.setItem("refresh_token", data.refreshToken);
        localStorage.setItem("user_data", JSON.stringify(data.user));
        localStorage.setItem("user_type", data.userType);
    },

    // التحقق من حالة المستخدم
    checkUserStatus(user: User): { isValid: boolean; message: string | null } {
        if (user.type === "admin" && user.status !== "active") {
            return {
                isValid: false,
                message:
                    "Your account is not active. Please contact administrator.",
            };
        }

        if (user.type === "client" || user.type === "Client") {
            const status = user.status?.toLowerCase();
            switch (status) {
                case "in_active":
                case "inactive":
                    return {
                        isValid: false,
                        message: "Your account is not active.",
                    };
                case "blocked":
                    return {
                        isValid: false,
                        message: "Your account has been blocked.",
                    };
                case "suspended":
                    return {
                        isValid: false,
                        message: "Your account has been suspended.",
                    };
                case "active":
                    return { isValid: true, message: null };
                default:
                    return {
                        isValid: false,
                        message: "Unknown account status.",
                    };
            }
        }

        return { isValid: true, message: null };
    },
};
