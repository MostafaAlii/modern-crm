import { useState } from "react";
import { useAuth } from "../../context/AuthContext";
import {
    BellIcon,
    SunIcon,
    MoonIcon,
    ChevronDownIcon,
    UserIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon,
    ShoppingBagIcon
} from "@heroicons/react/24/outline";

type Lang = "ltr" | "rtl";

interface ClientNavbarProps {
    darkMode: boolean;
    setDarkMode: (val: boolean) => void;
    rtl: boolean;
    setRtl: (val: boolean) => void;
}

export default function ClientNavbar({ darkMode, setDarkMode, rtl, setRtl }: ClientNavbarProps) {
    const [openDropdown, setOpenDropdown] = useState<"avatar" | "notif" | "lang" | null>(null);
    const { user, logout } = useAuth();

    const toggleDropdown = (name: "avatar" | "notif" | "lang") => {
        setOpenDropdown(openDropdown === name ? null : name);
    };

    const changeLang = (newLang: Lang) => {
        setRtl(newLang === "rtl");
        setOpenDropdown(null);
    };

    const handleLogout = () => {
        logout();
        window.location.href = "/client/login";
    };

    return (
        <header className="h-16 bg-white dark:bg-gray-800 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between px-6 shadow-md relative z-10">
            <div className="flex items-center">
                <ShoppingBagIcon className="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" />
                <h1 className="text-lg font-semibold text-slate-800 dark:text-slate-200">
                    Client Dashboard
                </h1>
            </div>

            <div className="flex items-center gap-4">
                {/* Notification */}
                <div className="relative">
                    <button
                        onClick={() => toggleDropdown("notif")}
                        className="relative p-2 rounded-full hover:bg-slate-100 dark:hover:bg-gray-700 transition cursor-pointer"
                    >
                        <BellIcon className="w-6 h-6 text-slate-700 dark:text-slate-200" />
                        <span className="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full animate-pulse" />
                    </button>
                </div>

                {/* Theme Toggle */}
                <button
                    onClick={() => setDarkMode(!darkMode)}
                    className="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-gray-700 transition cursor-pointer"
                >
                    {darkMode ? (
                        <MoonIcon className="w-6 h-6 text-slate-700 dark:text-slate-200" />
                    ) : (
                        <SunIcon className="w-6 h-6 text-slate-700 dark:text-slate-200" />
                    )}
                </button>

                {/* User Avatar */}
                <div className="relative">
                    <div
                        onClick={() => toggleDropdown("avatar")}
                        className="flex items-center gap-2 cursor-pointer"
                    >
                        <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold ring-2 ring-blue-300 hover:ring-blue-400 transition">
                            {user?.name?.charAt(0) || "C"}
                        </div>
                        <ChevronDownIcon className={`w-4 h-4 text-slate-500 dark:text-slate-300 transition-transform ${openDropdown === "avatar" ? "rotate-180" : "rotate-0"}`} />
                    </div>

                    {openDropdown === "avatar" && (
                        <div className={`absolute mt-2 w-48 bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl shadow-xl p-2 ${rtl ? "left-0" : "right-0"} z-50`}>
                            <div className="px-3 py-2 border-b border-slate-200 dark:border-gray-600">
                                <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{user?.name}</p>
                                <p className="text-xs text-slate-600 dark:text-slate-400">Client Account</p>
                            </div>

                            <a href="#" className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-600 text-slate-700 dark:text-slate-200 transition">
                                <UserIcon className="w-4 h-4" />
                                My Profile
                            </a>

                            <a href="#" className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-600 text-slate-700 dark:text-slate-200 transition">
                                <Cog6ToothIcon className="w-4 h-4" />
                                Account Settings
                            </a>

                            <button
                                onClick={handleLogout}
                                className="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 transition text-left"
                            >
                                <ArrowRightOnRectangleIcon className="w-4 h-4" />
                                Logout
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
