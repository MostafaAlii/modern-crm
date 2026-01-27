import { useState } from "react";

interface Props {
    value: boolean;
    onConfirm: (newStatus: boolean) => void;
}

export default function LanguageStatusSwitch({ value, onConfirm }: Props) {
    const [isActive, setIsActive] = useState(value);
    const [open, setOpen] = useState(false);
    const [pending, setPending] = useState(value);

    const toggle = () => {
        setPending(!isActive);
        setOpen(true);
    };

    const confirm = () => {
        setIsActive(pending);
        onConfirm(pending);
        setOpen(false);
    };

    return (
        <>
            <div className="flex items-center gap-2">
                <button
                    onClick={toggle}
                    className={`relative inline-flex h-6 w-12 rounded-full transition ${isActive ? "bg-green-500" : "bg-red-500"
                        }`}
                >
                    <span
                        className={`absolute top-1 left-1 h-4 w-4 bg-white rounded-full transition ${isActive ? "translate-x-6" : ""
                            }`}
                    />
                </button>
                <span className="text-xs font-semibold">
                    {isActive ? "Active" : "Inactive"}
                </span>
            </div>

            {open && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div
                        className="absolute inset-0 bg-black/40"
                        onClick={() => setOpen(false)}
                    />
                    <div className="relative bg-white dark:bg-slate-800 rounded-lg p-6 w-full max-w-sm">
                        <h3 className="font-semibold text-lg">
                            Confirm status change
                        </h3>

                        <p className="mt-2 text-sm">
                            Change status to{" "}
                            <b>{pending ? "Active" : "Inactive"}</b> ?
                        </p>

                        <div className="mt-4 flex justify-end gap-3">
                            <button
                                onClick={() => setOpen(false)}
                                className="px-4 py-2 bg-slate-200 rounded"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={confirm}
                                className={`px-4 py-2 text-white rounded ${pending ? "bg-green-600" : "bg-red-600"
                                    }`}
                            >
                                Yes
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
