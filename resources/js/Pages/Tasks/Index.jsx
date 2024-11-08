import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index({ auth, tasks }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Tareas" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="text-xl font-semibold text-gray-900">
                            Tareas
                        </h2>
                        <Link
                            href={route('tasks.create')}
                            className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Nueva Tarea
                        </Link>
                    </div>

                    <div className="space-y-6">
                        {tasks.map(task => (
                            <div key={task.id} className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 className="text-lg font-semibold">{task.title}</h3>
                                <p className="mt-2 text-gray-600">{task.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 