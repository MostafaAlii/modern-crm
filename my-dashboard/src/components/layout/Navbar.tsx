import { useState } from "react";
import {
    BellIcon,
    SunIcon,
    MoonIcon,
    ChevronDownIcon,
    UserIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon
} from "@heroicons/react/24/outline";
import { useAuth } from "../../context/AuthContext"; // تأكد من المسار الصحيح

type Lang = "ltr" | "rtl";

interface NavbarProps {
    darkMode: boolean;
    setDarkMode: (val: boolean) => void;
    rtl: boolean;
    setRtl: (val: boolean) => void;
}

export default function Navbar({ darkMode, setDarkMode, rtl, setRtl }: NavbarProps) {
    const [openDropdown, setOpenDropdown] = useState<"avatar" | "notif" | "lang" | null>(null);
    const { logout, user } = useAuth(); // إضافة useAuth

    const toggleDropdown = (name: "avatar" | "notif" | "lang") => {
        setOpenDropdown(openDropdown === name ? null : name);
    };

    const changeLang = (newLang: Lang) => {
        setRtl(newLang === "rtl");
        setOpenDropdown(null);
    };

    const handleLogout = () => {
        logout();
        window.location.href = "/admin/login";
    };

    return (
        <header className="h-16 bg-white dark:bg-gray-800 border-b border-slate-200 dark:border-gray-700 flex items-center justify-between px-6 shadow-md relative z-10">
            <h1 className="text-lg font-semibold text-slate-800 dark:text-slate-200">
                Admin Dashboard
            </h1>

            <div className="flex items-center gap-4">
                {/* Notification */}
                <div className="relative">
                    <button
                        onClick={() => toggleDropdown("notif")}
                        className="relative p-2 rounded-full hover:bg-slate-100 dark:hover:bg-gray-700 transition cursor-pointer"
                    >
                        <BellIcon className="w-6 h-6 text-slate-700 dark:text-slate-200" />
                        <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse" />
                    </button>

                    {openDropdown === "notif" && (
                        <div className={`absolute mt-2 w-64 bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl shadow-xl p-3 z-50 ${rtl ? "left-0" : "right-0"}`}>
                            <p className="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Notifications</p>
                            <div className="space-y-1 max-h-52 overflow-y-auto">
                                {["New user registered", "Server rebooted", "Payment received"].map((text) => (
                                    <a key={text} href="#" className="block px-3 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-600 text-slate-700 dark:text-slate-200 transition">
                                        {text}
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}
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

                {/* Language Selector */}
                <div className="relative">
                    <button
                        onClick={() => toggleDropdown("lang")}
                        className="flex items-center gap-1 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-700 transition cursor-pointer border border-slate-200 dark:border-gray-700"
                    >
                        {rtl ? "RTL" : "LTR"}
                        <ChevronDownIcon className={`w-4 h-4 text-slate-500 dark:text-slate-300 transition-transform ${openDropdown === "lang" ? "rotate-180" : "rotate-0"}`} />
                    </button>

                    {openDropdown === "lang" && (
                        <div className={`absolute mt-2 w-32 bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl shadow-xl p-2 z-50 ${rtl ? "left-0" : "right-0"}`}>
                            {["ltr", "rtl"].map((l) => (
                                <button
                                    key={l}
                                    onClick={() => changeLang(l as Lang)}
                                    className={`w-full text-left px-3 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-600 transition ${rtl === (l === "rtl") ? "font-semibold text-indigo-700 dark:text-indigo-300" : "text-slate-700 dark:text-slate-200"}`}
                                >
                                    {l.toUpperCase()}
                                </button>
                            ))}
                        </div>
                    )}
                </div>

                {/* User Avatar */}
                <div className="relative">
                    <div
                        onClick={() => toggleDropdown("avatar")}
                        className="flex items-center gap-2 cursor-pointer"
                    >
                        <div className="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold ring-2 ring-indigo-300 hover:ring-indigo-400 transition">
                            {user?.name?.charAt(0) || "A"}
                        </div>
                        <ChevronDownIcon className={`w-4 h-4 text-slate-500 dark:text-slate-300 transition-transform ${openDropdown === "avatar" ? "rotate-180" : "rotate-0"}`} />
                    </div>

                    {openDropdown === "avatar" && (
                        <div className={`absolute mt-2 w-48 bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl shadow-xl p-2 ${rtl ? "left-0" : "right-0"} z-50`}>
                            <div className="px-3 py-2 border-b border-slate-200 dark:border-gray-600">
                                <p className="text-sm font-medium text-slate-800 dark:text-slate-200">
                                    {user?.name || "Admin User"}
                                </p>
                                <p className="text-xs text-slate-600 dark:text-slate-400">
                                    {user?.email || "admin@example.com"}
                                </p>
                            </div>

                            <a href="/dashboard/profile" className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-600 text-slate-700 dark:text-slate-200 transition">
                                <UserIcon className="w-4 h-4" />
                                Profile
                            </a>

                            <a href="/dashboard/settings" className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-600 text-slate-700 dark:text-slate-200 transition">
                                <Cog6ToothIcon className="w-4 h-4" />
                                Settings
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
