import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Dashboard() {
    const [connectionStatus, setConnectionStatus] = useState('checking');

    useEffect(() => {
        // Verificar si existe el token en cache
        fetch('/api/hubstaff/check-connection')
            .then(response => response.json())
            .then(data => {
                setConnectionStatus(data.connected ? 'connected' : 'disconnected');
            })
            .catch(() => {
                setConnectionStatus('disconnected');
            });
    }, []);

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <p className="mb-4">You're logged in!</p>
                            
                            {connectionStatus === 'connected' ? (
                                <div className="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                    ✅ Conectado exitosamente con Hubstaff
                                </div>
                            ) : connectionStatus === 'disconnected' ? (
                                <a
                                    href="/hubstaff/connect"
                                    className="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                >
                                    Conectar con Hubstaff
                                </a>
                            ) : (
                                <div className="p-4 mb-4 text-sm text-gray-700 bg-gray-100 rounded-lg">
                                    Verificando conexión...
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}