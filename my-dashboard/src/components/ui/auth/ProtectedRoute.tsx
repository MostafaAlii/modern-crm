import { ReactNode, useEffect } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../../context/AuthContext';

interface ProtectedRouteProps {
    children: ReactNode;
    requiredUserType?: 'admin' | 'client';
}

export default function ProtectedRoute({
    children,
    requiredUserType
}: ProtectedRouteProps) {
    const { isAuthenticated, userType, isLoading } = useAuth();
    const location = useLocation();

    if (isLoading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        );
    }

    if (!isAuthenticated) {
        // إعادة التوجيه إلى الصفحة المناسبة بناءً على نوع المستخدم المطلوب
        const redirectTo = requiredUserType === 'admin' ? '/admin/login' : '/client/login';
        return <Navigate to={redirectTo} state={{ from: location }} replace />;
    }

    if (requiredUserType && userType !== requiredUserType) {
        return <Navigate to="/unauthorized" replace />;
    }

    return <>{children}</>;
}
