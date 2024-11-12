import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import { Link } from '@inertiajs/react';

export default function Show({ auth, project, tasks }) {
    console.log('Tasks data:', tasks); // Para debugging

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Proyecto: ${project.name}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <div>
                                    <Link 
                                        href={route('projects.index')} 
                                        className="text-blue-500 hover:text-blue-600 mb-2 inline-block"
                                    >
                                        ← Volver a Proyectos
                                    </Link>
                                    <h2 className="text-lg font-medium text-gray-900">
                                        {project.name}
                                    </h2>
                                </div>
                                <Link
                                    href={route('tasks.create', { project_id: project.id })}
                                    className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                                >
                                    Nueva Tarea
                                </Link>
                            </div>

                            {tasks.data.length > 0 ? (
                                <div className="space-y-4">
                                    {tasks.data.map(task => (
                                        <div key={task.id} className="border p-4 rounded-lg">
                                            <h3 className="font-semibold">{task.summary}</h3>
                                            {task.details && (
                                                <p className="text-gray-600 mt-2">{task.details}</p>
                                            )}
                                            <div className="mt-2 text-sm text-gray-500">
                                                Estado: {task.status}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-gray-500">
                                    No hay tareas disponibles para este proyecto.
                                </div>
                            )}

                            {tasks.data.length > 0 && (
                                <div className="mt-6">
                                    <Pagination meta={tasks.meta} />
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 