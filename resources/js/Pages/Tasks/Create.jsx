import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';

export default function Create({ auth, projects, users, hubstaffToken }) {
    const [generalError, setGeneralError] = useState('');
    const { data, setData, post, processing, errors, reset } = useForm({
        project_id: '',
        title: '',
        description: '',
        due_date: '',
        assignee_id: ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        setGeneralError('');

        post(route('tasks.store'), {
            onError: (errors) => {
                if (errors.error) {
                    setGeneralError(errors.error);
                }
            },
            onSuccess: () => {
                reset();
            },
        });
    };

console.log(projects)

    console.log('Token actual:', hubstaffToken);

    const testApiCall = () => {
        fetch('https://api.hubstaff.com/v2/projects/2638530/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${hubstaffToken}`
            },
            body: JSON.stringify({
                assignee_id: 2934955,
                summary: "Test task",
                metadata: [
                    {
                        key: "description",
                        value: "Test description"
                    }
                ]
            })
        })
        .then(response => response.json())
        .then(data => console.log('Respuesta:', data))
        .catch(error => console.error('Error:', error));
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Crear Tarea" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h2 className="text-lg font-medium text-gray-900 mb-6">
                                Crear Nueva Tarea
                            </h2>

                            {generalError && (
                                <div className="mb-4 p-4 text-red-700 bg-red-100 rounded-lg">
                                    {generalError}
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="project_id" value="Proyecto" />
                                    <select
                                        id="project_id"
                                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm ${errors.project_id ? 'border-red-500' : ''}`}
                                        value={data.project_id}
                                        onChange={e => setData('project_id', e.target.value)}
                                        required
                                    >
                                        <option value="">Seleccionar proyecto</option>
                                        {projects.map(project => (
                                            <option key={project.id} value={project.id}>
                                                {project.name}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError message={errors.project_id} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="title" value="Título" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        className={`mt-1 block w-full ${errors.title ? 'border-red-500' : ''}`}
                                        value={data.title}
                                        onChange={e => setData('title', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.title} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="description" value="Descripción" />
                                    <textarea
                                        id="description"
                                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm ${errors.description ? 'border-red-500' : ''}`}
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        rows="4"
                                    />
                                    <InputError message={errors.description} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="due_date" value="Fecha de Vencimiento" />
                                    <TextInput
                                        id="due_date"
                                        type="date"
                                        className={`mt-1 block w-full ${errors.due_date ? 'border-red-500' : ''}`}
                                        value={data.due_date}
                                        onChange={e => setData('due_date', e.target.value)}
                                    />
                                    <InputError message={errors.due_date} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="assignee_id" value="Asignar a" />
                                    <select
                                        id="assignee_id"
                                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm ${errors.assignee_id ? 'border-red-500' : ''}`}
                                        value={data.assignee_id}
                                        onChange={e => setData('assignee_id', e.target.value)}
                                    >
                                        <option value="">Seleccionar usuario</option>
                                        {users.map(user => (
                                            <option key={user.id} value={user.id}>
                                                {user.id} {user.name} ({user.email}) - {user.membership_role} 
                                                {user.effective_role !== user.membership_role && ` (${user.effective_role})`}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError message={errors.assignee_id} className="mt-2" />
                                </div>

                                <div className="flex items-center justify-end mt-4">
                                    <PrimaryButton className="ml-4" disabled={processing}>
                                        {processing ? 'Creando...' : 'Crear Tarea'}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 