import { useQuery } from "@tanstack/react-query";
import { useState, useMemo } from "react";
import { languagesApi, type LocalesParams } from "../services/api/languagesApi";

export function useLanguagesTable() {
    const [pagination, setPagination] = useState({
        pageIndex: 0,
        pageSize: 10,
    });

    const [statusFilter, setStatusFilter] = useState<
        "active" | "inactive" | null
    >(null);

    // Fetch paginated data
    const { data, isLoading, isError, error } = useQuery({
        queryKey: [
            "languages",
            pagination.pageIndex + 1,
            pagination.pageSize,
            statusFilter,
        ],
        queryFn: () =>
            languagesApi.getLocales({
                page: pagination.pageIndex + 1,
                per_page: pagination.pageSize,
                status: statusFilter,
            }),
        keepPreviousData: true,
    });

    // Fetch ALL data (للإحصائيات فقط) - cached permanently
    const { data: allData } = useQuery({
        queryKey: ["languages-all"],
        queryFn: () => languagesApi.getLocales({ per_page: 1000 }), // جيب كل اللغات مرة واحدة
        staleTime: Infinity, // Cache forever (أو حط 30 * 60 * 1000 للـ 30 دقيقة)
        cacheTime: Infinity,
    });

    // حساب الإحصائيات من الـ cached data
    const stats = useMemo(() => {
        if (!allData?.data) {
            return { total: 0, active: 0, inactive: 0 };
        }

        const total = allData.data.length;
        const active = allData.data.filter((lang) => lang.is_active).length;
        const inactive = allData.data.filter((lang) => !lang.is_active).length;

        return { total, active, inactive };
    }, [allData]);

    return {
        data: data?.data || [],
        pagination: data?.pagination,
        stats,
        isLoading,
        isError,
        error,
        paginationState: pagination,
        setPagination,
        statusFilter,
        setStatusFilter,
    };
}
