import React from 'react';

export default function BookingSummary({
    savedItems = [],
    currentItem = null,
    title = 'Booking Summary',
    onRemoveItem = null,
}) {
    const subtotal =
        savedItems.reduce((sum, item) => sum + Number(item.price || 0), 0) +
        0;

    return (
        <div className="card shadow-sm border-0 rounded-4">
            <div className="card-body p-4">
                <h4 className="fw-bold mb-3">{title}</h4>

                {savedItems.length === 0 && !currentItem?.serviceId ? (
                    <div className="text-muted">No services added yet.</div>
                ) : (
                    <>
                        {savedItems.length > 0 && (
                            <div className="d-flex flex-column gap-3 mb-4">
                                {savedItems.map((item, index) => (
                                    <div
                                        key={item.localId}
                                        className="border rounded-4 p-3"
                                    >
                                        <div className="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <div className="fw-bold mb-1">
                                                    {index + 1}. {item.serviceName || 'Service'}
                                                </div>

                                                <div className="small text-muted">
                                                    {item.durationMinutes
                                                        ? `${item.durationMinutes} mins`
                                                        : 'Duration not selected'}
                                                </div>

                                                <div className="small text-muted">
                                                    {item.employeeName || 'Employee not selected'}
                                                </div>

                                                <div className="small text-muted">
                                                    {item.date || 'Date not selected'}
                                                    {item.slot ? ` • ${item.slot}` : ''}
                                                </div>

                                                <div className="fw-semibold mt-2">
                                                    ${Number(item.price || 0).toFixed(2)}
                                                </div>
                                            </div>

                                            {onRemoveItem && (
                                                <button
                                                    type="button"
                                                    className="btn btn-sm btn-outline-danger rounded-pill"
                                                    onClick={() => onRemoveItem(item.localId)}
                                                >
                                                    Remove
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {currentItem?.serviceId && (
                            <div className="border-top pt-3 mb-3">
                                <div className="text-muted small mb-1">Current Selection</div>

                                <div className="fw-bold">
                                    {currentItem.serviceName || 'Service'}
                                </div>

                                <div className="small text-muted">
                                    {currentItem.durationMinutes
                                        ? `${currentItem.durationMinutes} mins`
                                        : 'Duration not selected'}
                                </div>

                                <div className="small text-muted">
                                    {currentItem.employeeName || 'Employee not selected'}
                                </div>

                                <div className="small text-muted">
                                    {currentItem.date || 'Date not selected'}
                                    {currentItem.slot ? ` • ${currentItem.slot}` : ''}
                                </div>

                                <div className="fw-semibold mt-2">
                                    ${Number(currentItem.price || 0).toFixed(2)}
                                </div>
                            </div>
                        )}

                        <div className="border-top pt-3">
                            <div className="d-flex justify-content-between align-items-center">
                                <span className="fw-medium">Subtotal</span>
                                <span className="fw-bold">
                                    ${subtotal.toFixed(2)}
                                </span>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}