import { useLanguagesTable } from '../../hooks/useLanguagesTable';
import LanguagesTable from '../../components/tables/LanguagesTable';
import { GlobeAltIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/react/24/outline';

export default function Languages() {
    const {
        data,
        pagination,
        stats,
        isLoading,
        isError,
        error,
        paginationState,
        setPagination,
        statusFilter,
        setStatusFilter,
    } = useLanguagesTable();

    const handlePageChange = (page: number) => {
        setPagination((prev) => ({ ...prev, pageIndex: page - 1 }));
    };

    const handlePageSizeChange = (size: number) => {
        setPagination({ pageIndex: 0, pageSize: size });
    };

    if (isError) {
        return (
            <div className="p-6">
                <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p className="text-red-800 dark:text-red-200">
                        Error loading languages: {(error as Error).message}
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-slate-800 dark:text-slate-200">
                        Languages Management
                    </h1>
                    <p className="mt-2 text-slate-600 dark:text-slate-400">
                        Manage system languages and localization settings
                    </p>
                </div>
            </div>

            {/* Statistics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Total Languages Card */}
                <div className="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-indigo-100 text-sm font-medium uppercase tracking-wide">
                                Total Languages
                            </p>
                            <p className="text-4xl font-bold mt-2">
                                {stats.total === 0 ? (
                                    <span className="animate-pulse">--</span>
                                ) : (
                                    stats.total
                                )}
                            </p>
                            <p className="text-indigo-200 text-sm mt-2">
                                Available in system
                            </p>
                        </div>
                        <div className="bg-white/20 rounded-full p-4 backdrop-blur-sm">
                            <GlobeAltIcon className="w-10 h-10" />
                        </div>
                    </div>
                </div>

                {/* Active Languages Card */}
                <div
                    className="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                    onClick={() => setStatusFilter('active')}
                >
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-green-100 text-sm font-medium uppercase tracking-wide">
                                Active Languages
                            </p>
                            <p className="text-4xl font-bold mt-2">
                                {stats.total === 0 ? (
                                    <span className="animate-pulse">--</span>
                                ) : (
                                    stats.active
                                )}
                            </p>
                            <p className="text-green-200 text-sm mt-2">
                                Currently enabled
                            </p>
                        </div>
                        <div className="bg-white/20 rounded-full p-4 backdrop-blur-sm">
                            <CheckCircleIcon className="w-10 h-10" />
                        </div>
                    </div>
                    {statusFilter === 'active' && (
                        <div className="mt-4 pt-4 border-t border-green-400/30">
                            <p className="text-green-100 text-xs">
                                ✓ Filter applied
                            </p>
                        </div>
                    )}
                </div>

                {/* Inactive Languages Card */}
                <div
                    className="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                    onClick={() => setStatusFilter('inactive')}
                >
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-red-100 text-sm font-medium uppercase tracking-wide">
                                Inactive Languages
                            </p>
                            <p className="text-4xl font-bold mt-2">
                                {stats.total === 0 ? (
                                    <span className="animate-pulse">--</span>
                                ) : (
                                    stats.inactive
                                )}
                            </p>
                            <p className="text-red-200 text-sm mt-2">
                                Currently disabled
                            </p>
                        </div>
                        <div className="bg-white/20 rounded-full p-4 backdrop-blur-sm">
                            <XCircleIcon className="w-10 h-10" />
                        </div>
                    </div>
                    {statusFilter === 'inactive' && (
                        <div className="mt-4 pt-4 border-t border-red-400/30">
                            <p className="text-red-100 text-xs">
                                ✓ Filter applied
                            </p>
                        </div>
                    )}
                </div>
            </div>

            {/* Filter Buttons */}
            <div className="flex items-center gap-3 bg-white dark:bg-slate-800 rounded-lg shadow p-4">
                <span className="text-sm font-medium text-slate-600 dark:text-slate-400">
                    Quick Filter:
                </span>
                <div className="flex gap-2">
                    <button
                        onClick={() => setStatusFilter(null)}
                        className={`px-4 py-2 rounded-lg font-medium transition-all duration-200 ${statusFilter === null
                                ? 'bg-indigo-600 text-white shadow-md scale-105'
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600'
                            }`}
                    >
                        All ({stats.total})
                    </button>
                    <button
                        onClick={() => setStatusFilter('active')}
                        className={`px-4 py-2 rounded-lg font-medium transition-all duration-200 ${statusFilter === 'active'
                                ? 'bg-green-600 text-white shadow-md scale-105'
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600'
                            }`}
                    >
                        Active ({stats.active})
                    </button>
                    <button
                        onClick={() => setStatusFilter('inactive')}
                        className={`px-4 py-2 rounded-lg font-medium transition-all duration-200 ${statusFilter === 'inactive'
                                ? 'bg-red-600 text-white shadow-md scale-105'
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600'
                            }`}
                    >
                        Inactive ({stats.inactive})
                    </button>
                </div>
            </div>

            {/* Table */}
            <div className="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
                <LanguagesTable
                    data={data}
                    isLoading={isLoading}
                    pagination={pagination}
                    onPageChange={handlePageChange}
                    onPageSizeChange={handlePageSizeChange}
                />
            </div>
        </div>
    );
}