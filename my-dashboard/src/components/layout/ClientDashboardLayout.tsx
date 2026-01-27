import { useState, useEffect } from "react";
import { Routes, Route } from "react-router-dom";
import ClientNavbar from "../layout/ClientNavbar";
import ClientSidebar from "../layout/ClientSidebar";

export default function ClientDashboardLayout() {
    const [darkMode, setDarkMode] = useState(false);
    const [rtl, setRtl] = useState(false);

    useEffect(() => {
        const html = document.documentElement;
        if (darkMode) {
            html.classList.add("dark");
        } else {
            html.classList.remove("dark");
        }
    }, [darkMode]);

    useEffect(() => {
        document.documentElement.dir = rtl ? "rtl" : "ltr";
    }, [rtl]);

    return (
        <div className="flex min-h-screen bg-gray-50 dark:bg-gray-900">
            <ClientSidebar rtl={rtl} darkMode={darkMode} />
            <div className="flex-1 flex flex-col">
                <ClientNavbar
                    darkMode={darkMode}
                    setDarkMode={setDarkMode}
                    rtl={rtl}
                    setRtl={setRtl}
                />
                <main className="flex-1 p-6">
                    <Routes>
                        <Route
                            index
                            element={
                                <div>
                                    <h2 className="text-2xl font-bold text-slate-800 dark:text-slate-200">
                                        Welcome to Client Dashboard
                                    </h2>
                                    <p className="mt-2 text-slate-600 dark:text-slate-300">
                                        This is your client dashboard.
                                    </p>
                                </div>
                            }
                        />
                        {/* Add more client-specific routes here */}
                    </Routes>
                </main>
            </div>
        </div>
    );
}
