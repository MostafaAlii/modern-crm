import { apiClient } from "./axios";

export interface Locale {
    code: string;
    name: string;
    native: string;
    script: string;
    regional: string;
    status: string;
    is_active: boolean;
}

export interface PaginationMeta {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
}

export interface LocalesResponse {
    success: boolean;
    message: string;
    data: Locale[];
    pagination: PaginationMeta;
}

export interface LocalesParams {
    page?: number;
    per_page?: number;
    status?: "active" | "inactive" | null;
}

export const languagesApi = {
    // Get all locales with pagination
    getLocales: async (
        params: LocalesParams = {},
    ): Promise<LocalesResponse> => {
        const { data } = await apiClient.get<LocalesResponse>("/locales", {
            params,
        });
        return data;
    },

    // Get active locales
    getActiveLocales: async (
        params: Omit<LocalesParams, "status"> = {},
    ): Promise<LocalesResponse> => {
        const { data } = await apiClient.get<LocalesResponse>(
            "/locales/active",
            { params },
        );
        return data;
    },

    // Get inactive locales
    getInactiveLocales: async (
        params: Omit<LocalesParams, "status"> = {},
    ): Promise<LocalesResponse> => {
        const { data } = await apiClient.get<LocalesResponse>(
            "/locales/inactive",
            { params },
        );
        return data;
    },
};
