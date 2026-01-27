import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { authService } from '../services/api/auth.service';
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

interface AuthContextType {
    user: User | null;
    token: string | null;
    isAuthenticated: boolean;
    userType: 'admin' | 'client' | null;
    isLoading: boolean;
    error: string | null;
    adminLogin: (credentials: { email: string; password: string }) => Promise<{ success: boolean; message: string }>;
    clientLogin: (credentials: { email: string; password: string }) => Promise<{ success: boolean; message: string }>;
    logout: () => void;
    clearError: () => void;
    refreshToken: () => Promise<boolean>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

interface AuthProviderProps {
    children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null);
    const [token, setToken] = useState<string | null>(null);
    const [userType, setUserType] = useState<'admin' | 'client' | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    // تحميل بيانات المصادقة من التخزين المحلي عند التحميل
    useEffect(() => {
        const initAuth = () => {
            const storedToken = authService.getToken();
            const storedUser = authService.getCurrentUser();
            const storedUserType = authService.getUserType();

            if (storedToken && storedUser && storedUserType) {
                setToken(storedToken);
                setUser(storedUser);
                setUserType(storedUserType);
            }
            setIsLoading(false);
        };

        initAuth();
    }, []);

    const adminLogin = async (
        credentials: { email: string; password: string }
    ): Promise<{ success: boolean; message: string }> => {
        setIsLoading(true);
        setError(null);

        try {
            const response = await authService.adminLogin(credentials);

            if (response.success && response.data) {
                // التحقق من حالة المستخدم
                const statusCheck = authService.checkUserStatus(response.data.user);

                if (!statusCheck.isValid) {
                    setError(statusCheck.message || 'Account status issue');
                    setIsLoading(false);
                    return { success: false, message: statusCheck.message || 'Account status issue' };
                }

                // حفظ البيانات
                authService.saveAuthData({
                    token: response.data.token,
                    refreshToken: response.data.refresh_token,
                    user: response.data.user,
                    userType: 'admin'
                });

                // تحديث الحالة
                setToken(response.data.token);
                setUser(response.data.user);
                setUserType('admin');

                return { success: true, message: response.message };
            } else {
                setError(response.message || 'Admin login failed');
                return { success: false, message: response.message || 'Admin login failed' };
            }
        } catch (err: any) {
            const errorMessage = err.response?.data?.message || 'An error occurred during admin login';
            setError(errorMessage);
            return { success: false, message: errorMessage };
        } finally {
            setIsLoading(false);
        }
    };

    const clientLogin = async (
        credentials: { email: string; password: string }
    ): Promise<{ success: boolean; message: string }> => {
        setIsLoading(true);
        setError(null);

        try {
            const response = await authService.clientLogin(credentials);

            if (response.success && response.data) {
                // التحقق من حالة المستخدم
                const statusCheck = authService.checkUserStatus(response.data.user);

                if (!statusCheck.isValid) {
                    setError(statusCheck.message || 'Account status issue');
                    setIsLoading(false);
                    return { success: false, message: statusCheck.message || 'Account status issue' };
                }

                // حفظ البيانات
                authService.saveAuthData({
                    token: response.data.token,
                    refreshToken: response.data.refresh_token,
                    user: response.data.user,
                    userType: 'client'
                });

                // تحديث الحالة
                setToken(response.data.token);
                setUser(response.data.user);
                setUserType('client');

                return { success: true, message: response.message };
            } else {
                setError(response.message || 'Client login failed');
                return { success: false, message: response.message || 'Client login failed' };
            }
        } catch (err: any) {
            const errorMessage = err.response?.data?.message || 'An error occurred during client login';
            setError(errorMessage);
            return { success: false, message: errorMessage };
        } finally {
            setIsLoading(false);
        }
    };

    const logout = () => {
        authService.logout();
        setUser(null);
        setToken(null);
        setUserType(null);
        setError(null);
    };

    const clearError = () => {
        setError(null);
    };

    const refreshToken = async (): Promise<boolean> => {
        const storedRefreshToken = authService.getRefreshToken();
        if (!storedRefreshToken) return false;

        try {
            const response = await authService.refreshToken(storedRefreshToken);

            if (response.success && response.data) {
                const currentUser = authService.getCurrentUser();
                const currentUserType = authService.getUserType();

                if (currentUser && currentUserType) {
                    authService.saveAuthData({
                        token: response.data.access_token,
                        refreshToken: response.data.refresh_token,
                        user: response.data.user || currentUser,
                        userType: currentUserType
                    });

                    setToken(response.data.access_token);
                    setUser(response.data.user || currentUser);
                    return true;
                }
            }
            return false;
        } catch (error) {
            logout();
            return false;
        }
    };

    const value: AuthContextType = {
        user,
        token,
        isAuthenticated: !!token && !!user,
        userType,
        isLoading,
        error,
        adminLogin,
        clientLogin,
        logout,
        clearError,
        refreshToken
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};
