import { Link } from '@inertiajs/react';

export default function Pagination({ meta }) {
    const { current_page, total, per_page } = meta;
    const totalPages = Math.ceil(total / per_page);
    
    if (totalPages <= 1) return null;

    const pages = [];
    const maxVisiblePages = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    return (
        <div className="flex items-center justify-center space-x-1">
            {/* Botón Anterior */}
            <Link
                preserveScroll
                href={`?page=${current_page - 1}`}
                className={`px-4 py-2 text-sm rounded-md 
                    ${current_page === 1 
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border'
                    }`}
                disabled={current_page === 1}
            >
                Anterior
            </Link>

            {/* Primera página si no es visible */}
            {startPage > 1 && (
                <>
                    <Link
                        preserveScroll
                        href="?page=1"
                        className="px-4 py-2 text-sm border rounded-md bg-white text-gray-700 hover:bg-gray-50"
                    >
                        1
                    </Link>
                    {startPage > 2 && (
                        <span className="px-4 py-2 text-gray-500">...</span>
                    )}
                </>
            )}

            {/* Páginas numeradas */}
            {pages.map(page => (
                <Link
                    key={page}
                    preserveScroll
                    href={`?page=${page}`}
                    className={`px-4 py-2 text-sm rounded-md 
                        ${page === current_page
                            ? 'bg-blue-500 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50 border'
                        }`}
                >
                    {page}
                </Link>
            ))}

            {/* Última página si no es visible */}
            {endPage < totalPages && (
                <>
                    {endPage < totalPages - 1 && (
                        <span className="px-4 py-2 text-gray-500">...</span>
                    )}
                    <Link
                        preserveScroll
                        href={`?page=${totalPages}`}
                        className="px-4 py-2 text-sm border rounded-md bg-white text-gray-700 hover:bg-gray-50"
                    >
                        {totalPages}
                    </Link>
                </>
            )}

            {/* Botón Siguiente */}
            <Link
                preserveScroll
                href={`?page=${current_page + 1}`}
                className={`px-4 py-2 text-sm rounded-md 
                    ${current_page === totalPages
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border'
                    }`}
                disabled={current_page === totalPages}
            >
                Siguiente
            </Link>
        </div>
    );
} 