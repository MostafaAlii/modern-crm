import { useState, useEffect } from "react";
import { Outlet } from "react-router-dom";
import Sidebar from "./Sidebar";
import Navbar from "./Navbar";

export default function DashboardLayout() {
    const [darkMode, setDarkMode] = useState(false);
    const [rtl, setRtl] = useState(false);

    useEffect(() => {
        if (darkMode) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    }, [darkMode]);

    useEffect(() => {
        document.documentElement.dir = rtl ? "rtl" : "ltr";
    }, [rtl]);

    return (
        <div className="min-h-screen flex bg-slate-100 dark:bg-gray-900">
            <Sidebar darkMode={darkMode} rtl={rtl} />
            <div className="flex-1 flex flex-col">
                <Navbar darkMode={darkMode} setDarkMode={setDarkMode} rtl={rtl} setRtl={setRtl} />
                <main className="flex-1 p-6">
                    <Outlet /> {/* هذا يعرض الصفحات الفرعية */}
                </main>
            </div>
        </div>
    );
}
