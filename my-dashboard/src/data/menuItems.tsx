import { ReactNode } from "react";
import { HomeIcon, Cog6ToothIcon, GlobeAltIcon } from "@heroicons/react/24/outline";

export interface MenuItemType {
    name: string;
    icon?: ReactNode;
    link?: string;
    children?: MenuItemType[];
}

export const menuItems: MenuItemType[] = [
    {
        name: "Dashboard",
        icon: <HomeIcon className="w-5 h-5" />,
        link: "/dashboard",
    },
    {
        name: "Main Settings",
        icon: <Cog6ToothIcon className="w-5 h-5" />,
        children: [
            {
                name: "Languages",
                icon: <GlobeAltIcon className="w-5 h-5" />,
                link: "/dashboard/settings/languages"
            },
            { name: "General", link: "/dashboard/settings/general" },
        ],
    },
];
