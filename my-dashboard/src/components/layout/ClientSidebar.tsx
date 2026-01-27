import { useState } from "react";
import {
    HomeIcon,
    UserIcon,
    ShoppingBagIcon,
    CreditCardIcon,
    ChatBubbleLeftRightIcon,
    Cog6ToothIcon,
    QuestionMarkCircleIcon,
    ArrowRightOnRectangleIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
} from "@heroicons/react/24/outline";
import { useAuth } from "../../context/AuthContext";

interface ClientSidebarProps {
    darkMode: boolean;
    rtl: boolean;
}

export default function ClientSidebar({ darkMode, rtl }: ClientSidebarProps) {
    const [collapsed, setCollapsed] = useState(false);
    const { logout } = useAuth();

    const menuItems = [
        { icon: HomeIcon, label: "Dashboard", path: "/client-dashboard" },
        { icon: UserIcon, label: "My Profile", path: "/client-dashboard/profile" },
        { icon: ShoppingBagIcon, label: "My Orders", path: "/client-dashboard/orders" },
        { icon: CreditCardIcon, label: "Payments", path: "/client-dashboard/payments" },
        { icon: ChatBubbleLeftRightIcon, label: "Support", path: "/client-dashboard/support" },
        { icon: Cog6ToothIcon, label: "Settings", path: "/client-dashboard/settings" },
        { icon: QuestionMarkCircleIcon, label: "Help Center", path: "/client-dashboard/help" },
    ];

    const handleLogout = () => {
        logout();
        window.location.href = "/client/login";
    };

    return (
        <aside className={`
            ${darkMode ? 'bg-gray-800' : 'bg-white'}
            ${collapsed ? 'w-20' : 'w-64'}
            border-r ${darkMode ? 'border-gray-700' : 'border-gray-200'}
            flex flex-col transition-all duration-300
            ${rtl ? 'border-l' : 'border-r'}
        `}>
            {/* Header */}
            <div className={`p-6 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                <div className="flex items-center justify-between">
                    {!collapsed && (
                        <div className="flex items-center">
                            <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                C
                            </div>
                            <div className="ml-3">
                                <h2 className="font-bold text-lg text-gray-800 dark:text-white">
                                    Client Portal
                                </h2>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    Welcome back!
                                </p>
                            </div>
                        </div>
                    )}
                    {collapsed && (
                        <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold mx-auto">
                            C
                        </div>
                    )}
                    <button
                        onClick={() => setCollapsed(!collapsed)}
                        className={`p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 ${collapsed ? 'mx-auto' : ''}`}
                    >
                        {rtl ? (
                            collapsed ? (
                                <ChevronDoubleLeftIcon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            ) : (
                                <ChevronDoubleRightIcon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            )
                        ) : (
                            collapsed ? (
                                <ChevronDoubleRightIcon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            ) : (
                                <ChevronDoubleLeftIcon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            )
                        )}
                    </button>
                </div>
            </div>

            {/* Menu Items */}
            <nav className="flex-1 p-4 space-y-2 overflow-y-auto">
                {menuItems.map((item) => {
                    const Icon = item.icon;
                    return (
                        <a
                            key={item.label}
                            href={item.path}
                            className={`
                                flex items-center ${collapsed ? 'justify-center' : 'px-4'}
                                py-3 rounded-xl transition-all
                                ${darkMode
                                    ? 'text-gray-300 hover:bg-gray-700 hover:text-white'
                                    : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'
                                }
                                ${location.pathname === item.path
                                    ? darkMode
                                        ? 'bg-blue-900/30 text-blue-300'
                                        : 'bg-blue-50 text-blue-600'
                                    : ''
                                }
                            `}
                        >
                            <Icon className="w-6 h-6" />
                            {!collapsed && <span className="ml-3 font-medium">{item.label}</span>}
                        </a>
                    );
                })}
            </nav>

            {/* Logout Button */}
            <div className="p-4 border-t dark:border-gray-700">
                <button
                    onClick={handleLogout}
                    className={`
                        w-full flex items-center ${collapsed ? 'justify-center' : 'px-4'}
                        py-3 rounded-xl transition-all
                        text-red-600 dark:text-red-400
                        hover:bg-red-50 dark:hover:bg-red-900/20
                    `}
                >
                    <ArrowRightOnRectangleIcon className="w-6 h-6" />
                    {!collapsed && <span className="ml-3 font-medium">Logout</span>}
                </button>
            </div>
        </aside>
    );
}
