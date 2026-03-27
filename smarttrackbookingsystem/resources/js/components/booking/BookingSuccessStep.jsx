import React from 'react';

function formatDate(dateString) {
    if (!dateString) return '—';

    try {
        const clean = String(dateString).split('T')[0];
        const [year, month, day] = clean.split('-').map(Number);

        if (!year || !month || !day) return dateString;

        const date = new Date(year, month - 1, day);

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return dateString;
    }
}

function formatTime(timeString) {
    if (!timeString) return '—';

    try {
        const clean = String(timeString).slice(0, 5); // HH:mm
        const [hour, minute] = clean.split(':').map(Number);

        if (Number.isNaN(hour) || Number.isNaN(minute)) return timeString;

        const date = new Date();
        date.setHours(hour, minute, 0, 0);

        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        });
    } catch {
        return timeString;
    }
}

export default function BookingSuccessStep({ bookingResponse, onBookAnother }) {
    const appointment = bookingResponse?.data?.appointment;
    const items = bookingResponse?.data?.items || [];

    return (
        <div className="container py-5">
            <div className="row justify-content-center">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-5">
                            <div className="text-center mb-4">
                                <div
                                    className="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success text-white"
                                    style={{ width: '72px', height: '72px', fontSize: '32px' }}
                                >
                                    ✓
                                </div>
                                <h2 className="fw-bold mb-2">Booking Confirmed</h2>
                                <p className="text-muted mb-0">
                                    Your appointment has been booked successfully.
                                </p>
                            </div>

                            <div className="border rounded-4 p-4 mb-4 bg-light">
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <div className="text-muted small">Appointment ID</div>
                                        <div className="fw-bold">#{appointment?.id || '—'}</div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="text-muted small">Status</div>
                                        <div className="fw-bold text-capitalize">
                                            {appointment?.status || '—'}
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="text-muted small">Booking Date</div>
                                        <div className="fw-bold">
                                            {formatDate(appointment?.appointment_date)}
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="text-muted small">Total Price</div>
                                        <div className="fw-bold">
                                            ${Number(appointment?.price || 0).toFixed(2)}
                                        </div>
                                    </div>

                                    <div className="col-md-12">
                                        <div className="text-muted small">Location</div>
                                        <div className="fw-bold">{appointment?.location || '—'}</div>
                                    </div>
                                </div>
                            </div>

                            <h5 className="fw-bold mb-3">Booked Services</h5>

                            <div className="d-flex flex-column gap-3">
                                {items.map((item, index) => (
                                    <div key={item.id || index} className="border rounded-4 p-3">
                                        <div className="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <div className="fw-bold">
                                                    {index + 1}. {item.service_name || 'Service'}
                                                </div>

                                                <div className="text-muted small">
                                                    {item.duration_minutes} mins
                                                </div>

                                                <div className="text-muted small">
                                                    {item.employee_name || '—'}
                                                </div>

                                                <div className="text-muted small">
                                                    {formatDate(item.appointment_date)} • {formatTime(item.start_time)} - {formatTime(item.end_time)}
                                                </div>
                                            </div>

                                            <div className="text-end">
                                                <div className="fw-bold">
                                                    ${Number(item.price || 0).toFixed(2)}
                                                </div>

                                                <div className="badge bg-success text-capitalize mt-2">
                                                    {item.status || 'confirmed'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="text-center mt-4">
                                <button
                                    type="button"
                                    className="btn btn-primary rounded-pill px-4"
                                    onClick={onBookAnother}
                                >
                                    Book Another Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}