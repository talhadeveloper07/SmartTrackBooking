import React from 'react';
import BookingSummary from './BookingSummary';

export default function StepSelectEmployee({
    currentItem,
    employeeOptions = [],
    savedItems = [],
    onSelectEmployee,
    onBack,
    onContinue,
}) {
    const canContinue = !!currentItem.employeeId;

    return (
        <div className="container py-5">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Select Employee</h2>
                                <p className="text-muted mb-0">
                                    Choose the employee who will perform this service.
                                </p>
                            </div>

                            {/* Selected Service + Duration Preview */}
                            <div className="card border-0 bg-light rounded-4 mb-4">
                                <div className="card-body d-flex align-items-center gap-3">
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

                            {/* Employee Cards */}
                            <div className="row g-4">
                                {employeeOptions.map((employee) => {
                                    const isSelected = currentItem.employeeId === employee.id;

                                    return (
                                        <div className="col-md-6 col-xl-4" key={employee.id}>
                                            <div
                                                className={`card h-100 border employee-card ${
                                                    isSelected ? 'border-primary shadow' : 'border-light'
                                                }`}
                                                style={{
                                                    cursor: 'pointer',
                                                    borderRadius: '20px',
                                                    transition: 'all 0.2s ease',
                                                    overflow: 'hidden',
                                                }}
                                                onClick={() => onSelectEmployee(employee)}
                                            >
                                                <div className="card-body text-center p-4">
                                                    <img
                                                        src={employee.image || '/images/profile/profile.png'}
                                                        alt={employee.name}
                                                        style={{
                                                            width: '90px',
                                                            height: '90px',
                                                            objectFit: 'cover',
                                                            borderRadius: '50%',
                                                            marginBottom: '16px',
                                                        }}
                                                    />

                                                    <h5 className="fw-bold mb-1">{employee.name}</h5>

                                                    {employee.designation && (
                                                        <div className="text-muted small mb-2">
                                                            {employee.designation}
                                                        </div>
                                                    )}

                                                    {employee.experience && (
                                                        <div className="small text-muted mb-3">
                                                            {employee.experience}
                                                        </div>
                                                    )}

                                                    {isSelected ? (
                                                        <span className="badge bg-primary px-3 py-2 rounded-pill">
                                                            Selected
                                                        </span>
                                                    ) : (
                                                        <span className="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                            Select
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>

                            {employeeOptions.length === 0 && (
                                <div className="alert alert-warning rounded-4 mt-3">
                                    No employees found for this service.
                                </div>
                            )}

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