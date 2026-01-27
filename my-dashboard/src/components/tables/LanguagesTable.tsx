import {
    useReactTable,
    getCoreRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    flexRender,
    type ColumnDef,
    type SortingState,
    type ColumnFiltersState,
} from '@tanstack/react-table';
import { useState } from 'react';
import {
    ChevronUpIcon,
    ChevronDownIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
} from '@heroicons/react/24/outline';
import type { Locale } from '../../services/api/languagesApi';
import { Dialog } from '@headlessui/react';
import LanguageStatusSwitch from "./LanguageStatusSwitch";
interface LanguagesTableProps {
    data: Locale[];
    isLoading: boolean;
    pagination?: {
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    onPageChange: (page: number) => void;
    onPageSizeChange: (size: number) => void;
}

export default function LanguagesTable({
    data,
    isLoading,
    pagination,
    onPageChange,
    onPageSizeChange,
}: LanguagesTableProps) {
    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);

    // Define columns
    const columns: ColumnDef<Locale>[] = [
        {
            id: 'row_number',
            header: '#',
            cell: ({ row }) => {
                // حساب رقم الصف الحقيقي بناءً على الصفحة الحالية
                const rowNumber = pagination
                    ? (pagination.current_page - 1) * pagination.per_page + row.index + 1
                    : row.index + 1;

                return (
                    <span className="font-semibold text-slate-500 dark:text-slate-400">
                        {rowNumber}
                    </span>
                );
            },
            size: 60,
        },
        {
            accessorKey: 'code',
            header: 'Code',
            cell: (info) => (
                <span className="font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                    {info.getValue() as string}
                </span>
            ),
        },
        {
            accessorKey: 'name',
            header: 'Name',
            cell: (info) => (
                <span className="font-medium text-slate-800 dark:text-slate-200">
                    {info.getValue() as string}
                </span>
            ),
        },
        {
            accessorKey: 'native',
            header: 'Native Name',
            cell: (info) => (
                <span className="text-slate-600 dark:text-slate-300">
                    {info.getValue() as string}
                </span>
            ),
        },
        {
            accessorKey: 'script',
            header: 'Script',
            cell: (info) => (
                <span className="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                    {info.getValue() as string}
                </span>
            ),
        },
        {
            accessorKey: 'regional',
            header: 'Regional',
            cell: (info) => (
                <span className="text-xs text-slate-500 dark:text-slate-400">
                    {info.getValue() as string}
                </span>
            ),
        },
        {
            accessorKey: "is_active",
            header: "Status",
            cell: ({ row }) => (
                <LanguageStatusSwitch
                    value={row.original.is_active}
                    onConfirm={(newStatus) => {
                        console.log("Update ID:", row.original.id, newStatus);
                        // هنا تحط API call
                    }}
                />
            ),
        },


    ];

    const table = useReactTable({
        data,
        columns,
        state: {
            sorting,
            columnFilters,
        },
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        manualPagination: true,
        pageCount: pagination?.last_page ?? -1,
    });

    if (isLoading) {
        return (
            <div className="animate-pulse space-y-4">
                {[...Array(5)].map((_, i) => (
                    <div key={i} className="h-16 bg-slate-200 dark:bg-slate-700 rounded" />
                ))}
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {/* Filters */}
            <div className="flex gap-4 items-center">
                <input
                    type="text"
                    placeholder="Search by name..."
                    className="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200"
                    onChange={(e) =>
                        table.getColumn('name')?.setFilterValue(e.target.value)
                    }
                />
                <select
                    className="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200"
                    onChange={(e) =>
                        table.getColumn('is_active')?.setFilterValue(e.target.value)
                    }
                >
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            {/* Table */}
            <div className="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                <table className="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead className="bg-slate-50 dark:bg-slate-800">
                        {table.getHeaderGroups().map((headerGroup) => (
                            <tr key={headerGroup.id}>
                                {headerGroup.headers.map((header) => (
                                    <th
                                        key={header.id}
                                        className={`px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider ${header.id !== 'row_number' ? 'cursor-pointer select-none' : ''
                                            }`}
                                        onClick={
                                            header.id !== 'row_number'
                                                ? header.column.getToggleSortingHandler()
                                                : undefined
                                        }
                                    >
                                        <div className="flex items-center gap-2">
                                            {flexRender(
                                                header.column.columnDef.header,
                                                header.getContext()
                                            )}
                                            {header.column.getIsSorted() === 'asc' && (
                                                <ChevronUpIcon className="w-4 h-4" />
                                            )}
                                            {header.column.getIsSorted() === 'desc' && (
                                                <ChevronDownIcon className="w-4 h-4" />
                                            )}
                                        </div>
                                    </th>
                                ))}
                            </tr>
                        ))}
                    </thead>
                    <tbody className="bg-white dark:bg-slate-900 divide-y divide-slate-200 dark:divide-slate-700">
                        {table.getRowModel().rows.map((row) => (
                            <tr
                                key={row.id}
                                className="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                            >
                                {row.getVisibleCells().map((cell) => (
                                    <td key={cell.id} className="px-6 py-4 whitespace-nowrap">
                                        {flexRender(
                                            cell.column.columnDef.cell,
                                            cell.getContext()
                                        )}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {pagination && (
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <span className="text-sm text-slate-700 dark:text-slate-300">
                            Showing {pagination.from} to {pagination.to} of {pagination.total} results
                        </span>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => onPageChange(1)}
                            disabled={pagination.current_page === 1}
                            className="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ChevronDoubleLeftIcon className="w-5 h-5" />
                        </button>
                        <button
                            onClick={() => onPageChange(pagination.current_page - 1)}
                            disabled={pagination.current_page === 1}
                            className="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ChevronLeftIcon className="w-5 h-5" />
                        </button>

                        <span className="px-4 py-2 text-sm">
                            Page {pagination.current_page} of {pagination.last_page}
                        </span>

                        <button
                            onClick={() => onPageChange(pagination.current_page + 1)}
                            disabled={pagination.current_page === pagination.last_page}
                            className="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ChevronRightIcon className="w-5 h-5" />
                        </button>
                        <button
                            onClick={() => onPageChange(pagination.last_page)}
                            disabled={pagination.current_page === pagination.last_page}
                            className="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ChevronDoubleRightIcon className="w-5 h-5" />
                        </button>

                        <select
                            value={pagination.per_page}
                            onChange={(e) => onPageSizeChange(Number(e.target.value))}
                            className="ml-4 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800"
                        >
                            {[10, 20, 30, 50, 100].map((size) => (
                                <option key={size} value={size}>
                                    {size} per page
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
            )}
        </div>
    );
}