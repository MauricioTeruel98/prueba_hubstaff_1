import { useState } from 'react';

export default function Alert({ type = 'error', message, onClose }) {
    const [isVisible, setIsVisible] = useState(true);

    const bgColor = type === 'error' ? 'bg-red-100' : 'bg-green-100';
    const textColor = type === 'error' ? 'text-red-900' : 'text-green-900';
    const borderColor = type === 'error' ? 'border-red-400' : 'border-green-400';

    const handleClose = () => {
        setIsVisible(false);
        if (onClose) onClose();
    };

    if (!isVisible || !message) return null;

    return (
        <div className={`${bgColor} border ${borderColor} rounded-lg p-4 mb-4`} role="alert">
            <div className="flex items-center justify-between">
                <div className={`flex items-center ${textColor}`}>
                    <span className="font-medium">{message}</span>
                </div>
                <button
                    onClick={handleClose}
                    className={`${textColor} hover:opacity-75`}
                >
                    <span className="sr-only">Cerrar</span>
                    <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    );
} 