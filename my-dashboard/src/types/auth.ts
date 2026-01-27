export interface LoginCredentials {
    email: string;
    password: string;
}

export interface User {
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

export interface AuthResponse {
    success: boolean;
    message: string;
    data: {
        user: User;
        token: string;
        refresh_token: string;
    };
    meta?: {
        expires_at: string;
    };
}

export interface RefreshTokenRequest {
    refresh_token: string;
}

export interface RefreshTokenResponse {
    success: boolean;
    message: string;
    data: {
        access_token: string;
        refresh_token: string;
        token_type: string;
        expires_at: string;
        user: User;
    };
}

export interface AuthState {
    user: User | null;
    token: string | null;
    refreshToken: string | null;
    isAuthenticated: boolean;
    userType: "admin" | "client" | null;
    isLoading: boolean;
    error: string | null;
}
