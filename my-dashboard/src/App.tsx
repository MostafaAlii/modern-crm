import { useState, useEffect } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { Toaster } from "react-hot-toast";

import Navbar from "./components/layout/Navbar";
import Sidebar from "./components/layout/Sidebar";
import Login from "./pages/auth/Login";
import Languages from "./pages/settings/Languages";

// Create a client
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
            retry: 1,
            staleTime: 5 * 60 * 1000, // 5 minutes
        },
    },
});

// Dashboard Layout Component
function DashboardLayout() {
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
        <div className="flex min-h-screen bg-gray-100 dark:bg-gray-900">
            <Sidebar rtl={rtl} darkMode={darkMode} />
            <div className="flex-1 flex flex-col">
                <Navbar
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
                                        Welcome to My Dashboard
                                    </h2>
                                    <p className="mt-2 text-slate-600 dark:text-slate-300">
                                        This is your modern business dashboard.
                                    </p>
                                </div>
                            }
                        />
                        {/* Settings Routes */}
                        <Route path="settings">
                            <Route path="languages" element={<Languages />} />
                            <Route path="general" element={<div>General Settings</div>} />
                        </Route>
                    </Routes>
                </main>
            </div>
        </div>
    );
}

// Main App with Routes
export default function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <Routes>
                {/* Login Route */}
                <Route path="/login" element={<Login />} />

                {/* Dashboard Routes */}
                <Route path="/dashboard/*" element={<DashboardLayout />} />

                {/* Default redirect to dashboard */}
                <Route path="/" element={<Navigate to="/dashboard" replace />} />
            </Routes>

            {/* Toast Notifications */}
            <Toaster
                position="top-right"
                toastOptions={{
                    duration: 3000,
                    style: {
                        background: "#363636",
                        color: "#fff",
                    },
                }}
            />

            {/* React Query Devtools */}
            <ReactQueryDevtools initialIsOpen={false} />
        </QueryClientProvider>
    );
}