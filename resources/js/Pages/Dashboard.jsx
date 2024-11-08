import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Dashboard({ hubstaffUser, hubstaffOrganization }) {
    const [connectionStatus, setConnectionStatus] = useState('checking');

    useEffect(() => {
        console.log('Hubstaff data:', { user: hubstaffUser, organization: hubstaffOrganization });
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
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <p className="mb-4">You're logged in!</p>
                            
                            {connectionStatus === 'connected' ? (
                                <div>
                                    <div className="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                        ✅ Conectado exitosamente con Hubstaff
                                    </div>
                                    
                                    {hubstaffUser && (
                                        <div className="mt-4">
                                            <h3 className="text-lg font-semibold mb-3">Información del Usuario</h3>
                                            <div className="bg-gray-50 p-4 rounded-lg mb-6">
                                                <p><strong>ID:</strong> {hubstaffUser.id}</p>
                                                <p><strong>Nombre:</strong> {hubstaffUser.name}</p>
                                                <p><strong>Email:</strong> {hubstaffUser.email}</p>
                                                <p><strong>Timezone:</strong> {hubstaffUser.timezone}</p>
                                                <p><strong>Estado:</strong> {hubstaffUser.status}</p>
                                                <p><strong>Última actividad:</strong> {new Date(hubstaffUser.last_activity).toLocaleString()}</p>
                                            </div>
                                        </div>
                                    )}
                                    
                                    {hubstaffOrganization && (
                                        <div className="mt-4">
                                            <h3 className="text-lg font-semibold mb-3">Información de la Organización</h3>
                                            <div className="bg-gray-50 p-4 rounded-lg">
                                                <p><strong>ID:</strong> {hubstaffOrganization.id}</p>
                                                <p><strong>Nombre:</strong> {hubstaffOrganization.name}</p>
                                                <p><strong>Estado:</strong> {hubstaffOrganization.status}</p>
                                                <p><strong>Zona Horaria:</strong> {hubstaffOrganization.time_zone}</p>
                                                <p><strong>Plan:</strong> {hubstaffOrganization.plan}</p>
                                                {hubstaffOrganization.last_activity && (
                                                    <p><strong>Última actividad:</strong> {new Date(hubstaffOrganization.last_activity).toLocaleString()}</p>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ) : connectionStatus === 'disconnected' ? (
                                <a href="/hubstaff/connect" className="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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