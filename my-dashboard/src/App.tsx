import { Routes, Route, Navigate } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { Toaster } from "react-hot-toast";

// Context
import { AuthProvider } from "./context/AuthContext";

// Components
import ProtectedRoute from "./components/ui/auth/ProtectedRoute";

// Pages
import Home from "./pages/dashboard/Home";
import AdminLogin from "./pages/auth/AdminLogin";
import ClientLogin from "./pages/auth/ClientLogin";
import DashboardLayout from "./components/layout/DashboardLayout";
import ClientDashboardLayout from "./components/layout/ClientDashboardLayout";
import Login from "./pages/auth/Login";

// Create a client
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
            retry: 1,
            staleTime: 5 * 60 * 1000,
        },
    },
});

// صفحة Home بسيطة للاختبار
const SimpleHome = () => (
    <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
            <h1 className="text-4xl font-bold mb-4">Welcome</h1>
            <div className="space-x-4">
                <a href="/admin/login" className="px-6 py-3 bg-indigo-600 text-white rounded-lg">
                    Admin Login
                </a>
                <a href="/client/login" className="px-6 py-3 bg-blue-600 text-white rounded-lg">
                    Client Login
                </a>
            </div>
        </div>
    </div>
);

// صفحة Dashboard Home بسيطة
const DashboardHome = () => (
    <div>
        <h2 className="text-2xl font-bold mb-4">Admin Dashboard</h2>
        <p>Welcome to admin panel</p>
    </div>
);

// صفحة Client Dashboard Home بسيطة
const ClientDashboardHome = () => (
    <div>
        <h2 className="text-2xl font-bold mb-4">Client Dashboard</h2>
        <p>Welcome to client panel</p>
    </div>
);

// صفحة Languages بسيطة
const Languages = () => (
    <div>
        <h2 className="text-2xl font-bold mb-4">Languages Settings</h2>
        <p>Manage your languages here</p>
    </div>
);

// Unauthorized Component
const Unauthorized = () => (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center p-8 bg-white rounded-2xl shadow-xl">
            <h1 className="text-3xl font-bold mb-2">Access Denied</h1>
            <p className="text-gray-600 mb-6">You don't have permission to access this page.</p>
            <a href="/" className="px-6 py-3 bg-indigo-600 text-white rounded-lg">
                Go Back Home
            </a>
        </div>
    </div>
);

export default function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <AuthProvider>
                <Routes>
                    {/* Public Routes */}
                    <Route path="/" element={<SimpleHome />} />
                    <Route path="/admin/login" element={<AdminLogin />} />
                    <Route path="/client/login" element={<ClientLogin />} />
                    <Route path="/unauthorized" element={<Unauthorized />} />

                    {/* Protected Admin Routes */}
                    <Route path="/dashboard" element={
                        <ProtectedRoute requiredUserType="admin">
                            <DashboardLayout />
                        </ProtectedRoute>
                    }>
                        <Route index element={<DashboardHome />} />
                        <Route path="settings">
                            <Route path="languages" element={<Languages />} />
                        </Route>
                    </Route>

                    {/* Protected Client Routes */}
                    <Route path="/client-dashboard" element={
                        <ProtectedRoute requiredUserType="client">
                            <ClientDashboardLayout />
                        </ProtectedRoute>
                    }>
                        <Route index element={<ClientDashboardHome />} />
                    </Route>

                    {/* Redirect all unknown paths to home */}
                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>

                {/* Toast Notifications */}
                <Toaster
                    position="top-right"
                    toastOptions={{
                        duration: 4000,
                        style: {
                            background: "#363636",
                            color: "#fff",
                            borderRadius: "12px",
                        },
                    }}
                />

                {/* React Query Devtools */}
                <ReactQueryDevtools initialIsOpen={false} />
            </AuthProvider>
        </QueryClientProvider>
    );
}
