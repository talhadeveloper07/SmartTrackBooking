import React, { useMemo, useState } from 'react';
import BookingSummary from './BookingSummary';

export default function CheckoutStep({
    businessSlug,
    savedItems = [],
    authData,
    authenticatedUser,
    onBack,
    onRemoveItem,
    onBookingSuccess,
}) {
    const [notes, setNotes] = useState('');
    const [location, setLocation] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');

    const subtotal = useMemo(() => {
        return savedItems.reduce((sum, item) => sum + Number(item.price || 0), 0);
    }, [savedItems]);

    const buildPayload = () => {
        const payload = {
            notes,
            location,
            items: savedItems.map((item) => ({
                service_id: item.serviceId,
                employee_id: item.employeeId,
                appointment_date: item.date,
                start_time: item.slot,
                duration_minutes: item.durationMinutes,
                price: item.price,
                location: item.location || location || null,
            })),
        };

        if (authenticatedUser?.customer_id) {
            payload.customer_id = authenticatedUser.customer_id;
        } else if (authenticatedUser?.id && authenticatedUser?.email) {
            payload.new_customer_name = authenticatedUser.name || '';
            payload.new_customer_email = authenticatedUser.email || '';
            payload.new_customer_phone = authData?.phone || '';
        } else {
            payload.new_customer_name = `${authData?.first_name || ''} ${authData?.last_name || ''}`.trim();
            payload.new_customer_email = authData?.email || '';
            payload.new_customer_phone = authData?.phone || '';
        }

        return payload;
    };

    const handleConfirmBooking = async () => {
        try {
            setLoading(true);
            setError('');
            setSuccessMessage('');

            const token = localStorage.getItem('booking_token');
            const payload = buildPayload();

            const response = await fetch(
                `http://127.0.0.1:8000/api/business/${businessSlug}/appointments/book`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        ...(token ? { Authorization: `Bearer ${token}` } : {}),
                    },
                    body: JSON.stringify(payload),
                }
            );

            const result = await response.json();

            if (!response.ok) {
                if (result?.errors) {
                    const firstKey = Object.keys(result.errors)[0];
                    const firstMessage = result.errors[firstKey]?.[0];
                    throw new Error(firstMessage || result.message || 'Booking failed.');
                }
                throw new Error(result.message || 'Booking failed.');
            }

            setSuccessMessage(result.message || 'Appointment booked successfully.');
            onBookingSuccess?.(result);
        } catch (err) {
            console.error(err);
            setError(err.message || 'Something went wrong while booking.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container py-5">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Step 6: Confirm Booking</h2>
                                <p className="text-muted mb-0">
                                    Review your selected services and confirm the appointment.
                                </p>
                            </div>

                            {error ? (
                                <div className="alert alert-danger rounded-4">{error}</div>
                            ) : null}

                            {successMessage ? (
                                <div className="alert alert-success rounded-4">{successMessage}</div>
                            ) : null}

                            <div className="row g-3">
                                <div className="col-md-12">
                                    <label className="form-label fw-semibold">Location (optional)</label>
                                    <input
                                        type="text"
                                        className="form-control rounded-4"
                                        placeholder="Enter location"
                                        value={location}
                                        onChange={(e) => setLocation(e.target.value)}
                                    />
                                </div>

                                <div className="col-md-12">
                                    <label className="form-label fw-semibold">Notes (optional)</label>
                                    <textarea
                                        className="form-control rounded-4"
                                        rows="4"
                                        placeholder="Add notes for your booking"
                                        value={notes}
                                        onChange={(e) => setNotes(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="border rounded-4 p-3 mt-4 bg-light">
                                <div className="d-flex justify-content-between">
                                    <span className="fw-semibold">Total Services</span>
                                    <span>{savedItems.length}</span>
                                </div>
                                <div className="d-flex justify-content-between mt-2">
                                    <span className="fw-semibold">Subtotal</span>
                                    <span>${subtotal.toFixed(2)}</span>
                                </div>
                            </div>

                            <div className="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary rounded-pill px-4"
                                    onClick={onBack}
                                    disabled={loading}
                                >
                                    Back
                                </button>

                                <button
                                    type="button"
                                    className="btn btn-primary rounded-pill px-4"
                                    onClick={handleConfirmBooking}
                                    disabled={loading || savedItems.length === 0}
                                >
                                    {loading ? 'Booking...' : 'Confirm Booking'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    <BookingSummary
                        savedItems={savedItems}
                        onRemoveItem={onRemoveItem}
                    />
                </div>
            </div>
        </div>
    );
}