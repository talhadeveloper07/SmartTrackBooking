import React, { useMemo } from 'react';
import DatePicker from 'react-datepicker';
import BookingSummary from './BookingSummary';

function parseLocalDate(dateString) {
    if (!dateString) return null;
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
}

function formatLocalDate(date) {
    if (!date) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

export default function StepSelectDateTime({
    currentItem,
    savedItems = [],
    availableDates = [],
    availableSlots = [],
    onDateChange,
    onSelectSlot,
    onBack,
    onContinue,
    loadingDates = false,
    loadingSlots = false,
}) {
    const canContinue = !!currentItem.date && !!currentItem.slot;

    const includeDates = useMemo(() => {
        return availableDates.map((date) => parseLocalDate(date));
    }, [availableDates]);

    const selectedDate = currentItem.date ? parseLocalDate(currentItem.date) : null;

    return (
        <div className="container py-5">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Select Date & Time</h2>
                                <p className="text-muted mb-0">
                                    Choose an available date and time slot for the selected employee.
                                </p>
                            </div>

                            <div className="card border-0 bg-light rounded-4 mb-4">
                                <div className="card-body">
                                    <div className="row g-3 align-items-center">
                                        <div className="col-md-8">
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={currentItem.serviceImage}
                                                    alt={currentItem.serviceName}
                                                    style={{
                                                        width: '90px',
                                                        height: '90px',
                                                        objectFit: 'cover',
                                                        borderRadius: '16px',
                                                    }}
                                                />

                                                <div>
                                                    <div className="text-muted small">Selected Service</div>
                                                    <h5 className="mb-1 fw-bold">{currentItem.serviceName}</h5>
                                                    <div className="small text-muted">
                                                        {currentItem.durationMinutes || '--'} min
                                                    </div>
                                                    <div className="fw-semibold text-primary">
                                                        ${Number(currentItem.price || 0).toFixed(2)}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="col-md-4 text-md-end">
                                            <div className="text-muted small">Employee</div>
                                            <div className="fw-bold">{currentItem.employeeName || '—'}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="mb-4">
    <label className="form-label fw-semibold">Select Available Date</label>

    <div className="border rounded-4 p-3 bg-white">
        {loadingDates ? (
            <div className="text-muted">Loading available dates...</div>
        ) : availableDates.length === 0 ? (
            <div className="text-muted">
                No available dates found for this employee.
            </div>
        ) : (
            <DatePicker
                inline
                selected={selectedDate}
                onChange={(date) => {
                    if (!date) return;
                    onDateChange(formatLocalDate(date));
                }}
                includeDates={includeDates}
                minDate={new Date()}
                calendarClassName="booking-datepicker"
            />
        )}
    </div>

    <div className="small text-muted mt-2">
        Only highlighted dates are selectable.
    </div>
</div>

                            <div>
                                <h5 className="fw-bold mb-3">Available Time Slots</h5>

                                {loadingSlots ? (
                                    <div className="alert alert-info rounded-4 mb-0">
                                        Loading available slots...
                                    </div>
                                ) : !currentItem.date ? (
                                    <div className="alert alert-light rounded-4 mb-0">
                                        Please select a date first.
                                    </div>
                                ) : availableSlots.length === 0 ? (
                                    <div className="alert alert-warning rounded-4 mb-0">
                                        No available slots found for this date.
                                    </div>
                                ) : (
                                    <div className="row g-3">
                                        {availableSlots.map((slot) => {
                                            const isSelected = currentItem.slot === slot;

                                            return (
                                                <div className="col-md-4 col-sm-6" key={slot}>
                                                    <button
                                                        type="button"
                                                        className={`btn w-100 py-3 rounded-4 ${
                                                            isSelected
                                                                ? 'btn-primary'
                                                                : 'btn-outline-secondary'
                                                        }`}
                                                        onClick={() => onSelectSlot(slot)}
                                                    >
                                                        {slot}
                                                    </button>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>

                            <div className="d-flex justify-content-between mt-4">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary px-4 py-2 rounded-pill"
                                    onClick={onBack}
                                >
                                    Back
                                </button>

                                <button
                                    type="button"
                                    className="btn btn-primary px-4 py-2 rounded-pill"
                                    disabled={!canContinue}
                                    onClick={onContinue}
                                >
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    <BookingSummary
                        savedItems={savedItems}
                        currentItem={currentItem}
                    />
                </div>
            </div>
        </div>
    );
}