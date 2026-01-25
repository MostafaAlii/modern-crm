import { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { ChevronDownIcon } from "@heroicons/react/24/outline";
import { menuItems } from "../../data/menuItems";
import type { MenuItemType } from "../../data/menuItems";
interface SidebarProps {
    rtl?: boolean;
    darkMode?: boolean;
}

export default function Sidebar({ rtl = false, darkMode = false }: SidebarProps) {
    const [collapsed, setCollapsed] = useState(false);
    const [openMenus, setOpenMenus] = useState<string[]>([]);
    const location = useLocation();

    // Accordion behavior: close siblings when opening a menu
    const toggleMenu = (menuPath: string) => {
        setOpenMenus((prev) => {
            const isCurrentlyOpen = prev.includes(menuPath);

            if (isCurrentlyOpen) {
                // Close this menu and all its children
                return prev.filter((path) => !path.startsWith(menuPath + ">") && path !== menuPath);
            } else {
                // Close all menus at the same level (siblings)
                const pathParts = menuPath.split(">");
                const parentPath = pathParts.slice(0, -1).join(">");

                const filteredMenus = prev.filter((path) => {
                    const parts = path.split(">");
                    const currentParent = parts.slice(0, -1).join(">");

                    // Keep menus that:
                    // 1. Are not siblings (different parent)
                    // 2. Are parents of the current menu
                    return currentParent !== parentPath || menuPath.startsWith(path);
                });

                return [...filteredMenus, menuPath];
            }
        });
    };

    const isMenuOpen = (menuPath: string) => openMenus.includes(menuPath);

    // Check if current path matches item link
    const isActive = (link?: string) => {
        if (!link) return false;
        return location.pathname === link || location.pathname.startsWith(link + "/");
    };

    const handleItemClick = (item: MenuItemType, menuPath: string, hasChildren: boolean) => {
        if (hasChildren) {
            if (!collapsed) toggleMenu(menuPath);
        }
    };

    const renderMenuItem = (item: MenuItemType, level = 0, parentPath = "") => {
        const hasChildren = item.children && item.children.length > 0;
        const menuPath = parentPath ? `${parentPath}>${item.name}` : item.name;
        const isOpen = isMenuOpen(menuPath);
        const isItemActive = isActive(item.link);
        const paddingStart = rtl ? "pr" : "pl";
        const paddingClass = collapsed ? "" : `${paddingStart}-${level * 4 + 3}`;

        // Wrapper component - Link if has link, button otherwise
        const WrapperComponent = item.link && !hasChildren ? Link : "button";
        const wrapperProps = item.link && !hasChildren
            ? { to: item.link }
            : { onClick: () => handleItemClick(item, menuPath, hasChildren) };

        return (
            <div key={menuPath} className="flex flex-col w-full group relative">
                {/* Main Menu Item */}
                <WrapperComponent
                    {...wrapperProps}
                    className={`
                        w-full flex items-center gap-3 p-3 rounded-lg
                        text-slate-700 dark:text-slate-200
                        hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 
                        dark:hover:from-indigo-900 dark:hover:to-indigo-800
                        hover:text-indigo-600 dark:hover:text-indigo-300
                        hover:shadow-md hover:scale-[1.02]
                        active:scale-[0.98]
                        transition-all duration-300 ease-out
                        ${collapsed ? "justify-center" : paddingClass}
                        ${isItemActive && !hasChildren
                            ? "bg-gradient-to-r from-indigo-100 to-indigo-50 dark:from-indigo-900 dark:to-indigo-800 text-indigo-700 dark:text-indigo-300 font-semibold shadow-md"
                            : ""
                        }
                        ${level > 0 ? "text-sm" : ""}
                    `}
                >
                    {/* Icon with animation */}
                    <span className={`flex-shrink-0 transition-transform duration-300 ${isOpen ? "rotate-12 scale-110" : ""}`}>
                        {item.icon}
                    </span>

                    {!collapsed && (
                        <>
                            <span className="flex-1 text-start transition-all duration-200">{item.name}</span>

                            {/* Chevron with smooth rotation */}
                            {hasChildren && (
                                <ChevronDownIcon
                                    className={`
                                        w-4 h-4 transition-all duration-500 ease-out
                                        ${isOpen ? "rotate-180 text-indigo-600 dark:text-indigo-400" : "rotate-0"}
                                    `}
                                />
                            )}
                        </>
                    )}
                </WrapperComponent>

                {/* Submenu with slide + fade animation */}
                {hasChildren && !collapsed && (
                    <div
                        className={`
                            overflow-hidden transition-all duration-500 ease-in-out
                            ${isOpen
                                ? "max-h-[1000px] opacity-100 mt-1 translate-y-0"
                                : "max-h-0 opacity-0 -translate-y-2"
                            }
                        `}
                    >
                        <div
                            className={`
                                flex flex-col space-y-1 
                                ${rtl ? "mr-2" : "ml-2"}
                                border-l-2 border-indigo-200 dark:border-indigo-800
                                pl-2
                            `}
                        >
                            {item.children!.map((child) => renderMenuItem(child, level + 1, menuPath))}
                        </div>
                    </div>
                )}

                {/* Tooltip for collapsed sidebar */}
                {collapsed && (
                    <div
                        className={`
                            absolute z-50 top-2 ${rtl ? "right-full mr-2" : "left-full ml-2"}
                            whitespace-nowrap px-3 py-2 rounded-lg 
                            bg-gray-900 dark:bg-gray-700 text-white text-sm
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible
                            transition-all duration-300 ease-out
                            shadow-xl
                            before:content-[''] before:absolute before:top-1/2 before:-translate-y-1/2
                            ${rtl ? "before:right-[-6px]" : "before:left-[-6px]"}
                            before:border-8 before:border-transparent
                            ${rtl ? "before:border-r-gray-900 dark:before:border-r-gray-700" : "before:border-l-gray-900 dark:before:border-l-gray-700"}
                        `}
                    >
                        {item.name}
                    </div>
                )}
            </div>
        );
    };

    return (
        <aside
            className={`
                bg-white dark:bg-gray-800 
                border-${rtl ? "l" : "r"} border-slate-200 dark:border-gray-700 
                min-h-screen flex flex-col 
                transition-all duration-500 ease-in-out
                shadow-xl
                ${collapsed ? "w-20" : "w-64"}
            `}
            dir={rtl ? "rtl" : "ltr"}
        >
            {/* Header */}
            <div className="flex items-center justify-between p-6 border-b border-slate-200 dark:border-gray-700">
                {!collapsed && (
                    <h1 className="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent animate-pulse">
                        {rtl ? "لوحة التحكم" : "My Dashboard"}
                    </h1>
                )}
                <button
                    onClick={() => setCollapsed(!collapsed)}
                    className="p-2 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900 dark:hover:to-purple-900 transition-all duration-300 cursor-pointer hover:scale-110 active:scale-95"
                    aria-label="Toggle sidebar"
                >
                    <svg
                        className={`w-6 h-6 text-slate-700 dark:text-slate-200 transition-transform duration-500 ${collapsed ? "rotate-180" : "rotate-0"
                            }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
            </div>

            {/* Navigation with custom scrollbar */}
            <nav className="mt-6 flex-1 flex flex-col gap-1 px-3 overflow-y-auto scrollbar-thin scrollbar-thumb-indigo-300 dark:scrollbar-thumb-indigo-700 scrollbar-track-transparent">
                {menuItems.map((item) => renderMenuItem(item))}
            </nav>

            {/* Footer */}
            <div className="mt-auto p-6 border-t border-slate-200 dark:border-gray-700">
                {!collapsed && (
                    <div className="text-sm text-slate-500 dark:text-slate-400 select-none text-center hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-300">
                        {rtl ? "© 2026 شركتي" : "© 2026 MyCompany"}
                    </div>
                )}
            </div>
        </aside>
    );
}