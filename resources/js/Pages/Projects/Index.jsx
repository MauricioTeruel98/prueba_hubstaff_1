import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';

export default function Index({ auth, projects }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Proyectos" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h2 className="text-lg font-medium text-gray-900 mb-6">
                                Proyectos
                            </h2>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {projects.map(project => (
                                    <Link 
                                        key={project.id}
                                        href={route('projects.show', project.id)}
                                        className="block p-6 bg-white border rounded-lg hover:bg-gray-50"
                                    >
                                        <h3 className="text-xl font-semibold mb-2">{project.name}</h3>
                                        <p className="text-gray-600">{project.description}</p>
                                        <div className="mt-4 text-sm text-gray-500">
                                            {project.status}
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 