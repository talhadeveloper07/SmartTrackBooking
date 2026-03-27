import React, { useEffect, useMemo, useState } from 'react';
import StepSelectService from './booking/StepSelectService';
import StepSelectDuration from './booking/StepSelectDuration';
import StepSelectEmployee from './booking/StepSelectEmployee';
import StepSelectDateTime from './booking/StepSelectDateTime';
import AuthStep from './booking/AuthStep';
import CheckoutStep from './booking/CheckoutStep';
import BookingSuccessStep from './booking/BookingSuccessStep';

const emptyServiceItem = (id) => ({
    localId: id,
    serviceId: '',
    serviceName: '',
    serviceImage: '',
    durationId: '',
    durationMinutes: '',
    price: 0,
    employeeId: '',
    employeeName: '',
    employeeImage: '',
    date: '',
    slot: '',
});

export default function CustomerAppointmentStepForm({ businessSlug }) {
    const [step, setStep] = useState(1);
    const [savedItems, setSavedItems] = useState([]);

    const [servicesData, setServicesData] = useState([]);
    const [loadingServices, setLoadingServices] = useState(true);
    const [servicesError, setServicesError] = useState('');

    const [selectedServiceDetails, setSelectedServiceDetails] = useState(null);
    const [loadingServiceDetails, setLoadingServiceDetails] = useState(false);
    const [serviceDetailsError, setServiceDetailsError] = useState('');

    const [availableDates, setAvailableDates] = useState([]);
    const [availableSlots, setAvailableSlots] = useState([]);
    const [loadingDates, setLoadingDates] = useState(false);
    const [loadingSlots, setLoadingSlots] = useState(false);
    const [availabilityError, setAvailabilityError] = useState(''); 

    const [authMode, setAuthMode] = useState('login');
const [authData, setAuthData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
});
const [authLoading, setAuthLoading] = useState(false);
const [authError, setAuthError] = useState('');
const [authenticatedUser, setAuthenticatedUser] = useState(null);
const [bookingResponse, setBookingResponse] = useState(null);

    const [currentItem, setCurrentItem] = useState(emptyServiceItem(Date.now()));

    useEffect(() => {
        const fetchServices = async () => {
            try {
                setLoadingServices(true);
                setServicesError('');

                const response = await fetch(
                    `http://127.0.0.1:8000/api/business/${businessSlug}/services`
                );

                if (!response.ok) {
                    throw new Error('Failed to fetch services');
                }

                const result = await response.json();

                const formattedServices = (result.data || []).map((service) => ({
                    id: service.id,
                    name: service.name,
                    image: service.image || '/images/services/default-service.jpg',
                    short_description: service.description || '',
                }));

                setServicesData(formattedServices);
            } catch (error) {
                console.error(error);
                setServicesError('Unable to load services.');
            } finally {
                setLoadingServices(false);
            }
        };

        if (businessSlug) {
            fetchServices();
        }
    }, [businessSlug]);

    const fetchServiceDetails = async (serviceId) => {
        try {
            setLoadingServiceDetails(true);
            setServiceDetailsError('');

            const response = await fetch(
                `http://127.0.0.1:8000/api/business/${businessSlug}/services/${serviceId}`
            );

            if (!response.ok) {
                throw new Error('Failed to fetch service details');
            }

            const result = await response.json();
            const data = result.data || {};

            setSelectedServiceDetails({
                service: data.service || null,
                durations: (data.durations || []).map((duration) => ({
                    id: duration.id,
                    duration_minutes: duration.duration_minutes,
                    price: Number(duration.price || 0),
                })),
                employees: (data.employees || []).map((employee) => ({
                    id: employee.id,
                    name: employee.name,
                    image: employee.image || '/images/profile/profile.png',
                    designation: employee.designation || '',
                    experience: employee.experience || '',
                })),
            });
        } catch (error) {
            console.error(error);
            setServiceDetailsError('Unable to load service details.');
            setSelectedServiceDetails(null);
        } finally {
            setLoadingServiceDetails(false);
        }
    };

    const fetchAvailableDates = async ({ serviceId, employeeId, durationMinutes }) => {
        try {
            setLoadingDates(true);
            setAvailabilityError('');
            setAvailableDates([]);
            setAvailableSlots([]);

            const params = new URLSearchParams({
                service_id: serviceId,
                employee_id: employeeId,
                duration_minutes: durationMinutes,
            });

            const response = await fetch(
                `http://127.0.0.1:8000/api/business/${businessSlug}/available-dates?${params.toString()}`
            );

            if (!response.ok) {
                throw new Error('Failed to fetch available dates');
            }

            const result = await response.json();
            setAvailableDates(result?.data?.available_dates || []);
        } catch (error) {
            console.error(error);
            setAvailabilityError('Unable to load available dates.');
            setAvailableDates([]);
        } finally {
            setLoadingDates(false);
        }
    };

    const fetchAvailableSlots = async ({ serviceId, employeeId, durationMinutes, date }) => {
        try {
            setLoadingSlots(true);
            setAvailabilityError('');
            setAvailableSlots([]);

            const params = new URLSearchParams({
                service_id: serviceId,
                employee_id: employeeId,
                duration_minutes: durationMinutes,
                appointment_date: date,
            });

            const response = await fetch(
                `http://127.0.0.1:8000/api/business/${businessSlug}/available-slots?${params.toString()}`
            );

            if (!response.ok) {
                throw new Error('Failed to fetch available slots');
            }

            const result = await response.json();
            setAvailableSlots(result?.data?.slots || []);
        } catch (error) {
            console.error(error);
            setAvailabilityError('Unable to load available slots.');
            setAvailableSlots([]);
        } finally {
            setLoadingSlots(false);
        }
    };

    const handleSelectService = async (service) => {
        setCurrentItem((prev) => ({
            ...prev,
            serviceId: service.id,
            serviceName: service.name,
            serviceImage: service.image,
            durationId: '',
            durationMinutes: '',
            price: 0,
            employeeId: '',
            employeeName: '',
            employeeImage: '',
            date: '',
            slot: '',
        }));

        setAvailableDates([]);
        setAvailableSlots([]);
        await fetchServiceDetails(service.id);
    };

    const handleSelectDuration = (duration) => {
        setCurrentItem((prev) => ({
            ...prev,
            durationId: duration.id,
            durationMinutes: duration.duration_minutes,
            price: duration.price,
            employeeId: '',
            employeeName: '',
            employeeImage: '',
            date: '',
            slot: '',
        }));

        setAvailableDates([]);
        setAvailableSlots([]);
    };

    const handleSelectEmployee = async (employee) => {
        const updatedItem = {
            ...currentItem,
            employeeId: employee.id,
            employeeName: employee.name,
            employeeImage: employee.image || '',
            date: '',
            slot: '',
        };

        setCurrentItem(updatedItem);
        setAvailableSlots([]);

        if (
            updatedItem.serviceId &&
            updatedItem.employeeId &&
            updatedItem.durationMinutes
        ) {
            await fetchAvailableDates({
                serviceId: updatedItem.serviceId,
                employeeId: updatedItem.employeeId,
                durationMinutes: updatedItem.durationMinutes,
            });
        }
    };

    const handleDateChange = async (date) => {
        const updatedItem = {
            ...currentItem,
            date,
            slot: '',
        };

        setCurrentItem(updatedItem);

        if (
            updatedItem.serviceId &&
            updatedItem.employeeId &&
            updatedItem.durationMinutes &&
            updatedItem.date
        ) {
            await fetchAvailableSlots({
                serviceId: updatedItem.serviceId,
                employeeId: updatedItem.employeeId,
                durationMinutes: updatedItem.durationMinutes,
                date: updatedItem.date,
            });
        }
    };

    const handleSelectSlot = (slot) => {
        setCurrentItem((prev) => ({
            ...prev,
            slot,
        }));
    };

    const durationOptions = useMemo(() => {
        return selectedServiceDetails?.durations || [];
    }, [selectedServiceDetails]);

    const employeeOptions = useMemo(() => {
        return selectedServiceDetails?.employees || [];
    }, [selectedServiceDetails]);

    const isDuplicateSelection = (item) => {
        return savedItems.some((saved) =>
            String(saved.serviceId) === String(item.serviceId) &&
            String(saved.durationId) === String(item.durationId) &&
            String(saved.employeeId) === String(item.employeeId) &&
            String(saved.date) === String(item.date) &&
            String(saved.slot) === String(item.slot)
        );
    };

    const handleFinishCurrentService = () => {
        if (
            !currentItem.serviceId ||
            !currentItem.durationId ||
            !currentItem.employeeId ||
            !currentItem.date ||
            !currentItem.slot
        ) {
            return;
        }

        if (isDuplicateSelection(currentItem)) {
            alert('This same service, duration, employee, date and slot is already selected.');
            return;
        }

        setSavedItems((prev) => [...prev, { ...currentItem }]);

        setCurrentItem(emptyServiceItem(Date.now()));
        setSelectedServiceDetails(null);
        setAvailableDates([]);
        setAvailableSlots([]);
        setStep(5);
    };

    const handleRemoveItem = (localId) => {
        setSavedItems((prev) => prev.filter((item) => item.localId !== localId));
    };

    const handleAddMoreService = () => {
        setCurrentItem(emptyServiceItem(Date.now()));
        setSelectedServiceDetails(null);
        setAvailableDates([]);
        setAvailableSlots([]);
        setAvailabilityError('');
        setStep(1);
    };

   const handleAuthContinue = async () => {
    try {
        setAuthLoading(true);
        setAuthError('');

        const url =
            authMode === 'login'
                ? `http://127.0.0.1:8000/api/business/${businessSlug}/auth/login`
                : `http://127.0.0.1:8000/api/business/${businessSlug}/auth/register`;

        const payload =
            authMode === 'login'
                ? {
                      email: authData.email,
                      password: authData.password,
                  }
                : {
                      first_name: authData.first_name,
                      last_name: authData.last_name,
                      email: authData.email,
                      phone: authData.phone,
                      password: authData.password,
                  };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Authentication failed.');
        }

        if (result?.data?.token) {
            localStorage.setItem('booking_token', result.data.token);
        }

        if (result?.data?.user) {
            localStorage.setItem('booking_user', JSON.stringify(result.data.user));
            setAuthenticatedUser({
                ...result.data.user,
                customer_id: result?.data?.customer?.id || null,
            });
        }

        setStep(6);
    } catch (error) {
        console.error(error);
        setAuthError(error.message || 'Something went wrong.');
    } finally {
        setAuthLoading(false);
    }
};

    if (loadingServices) {
        return (
            <div className="container py-5">
                <div className="alert alert-info rounded-4">Loading services...</div>
            </div>
        );
    }

    if (servicesError) {
        return (
            <div className="container py-5">
                <div className="alert alert-danger rounded-4">{servicesError}</div>
            </div>
        );
    }

    const handleBackToLastService = async () => {
    if (savedItems.length === 0) {
        setStep(1);
        return;
    }

    const lastItem = savedItems[savedItems.length - 1];

    // remove last item from summary and put it back into current item
    setSavedItems((prev) => prev.slice(0, -1));
    setCurrentItem({ ...lastItem });

    // reload service details
    if (lastItem.serviceId) {
        await fetchServiceDetails(lastItem.serviceId);
    }

    // reload available dates
    if (lastItem.serviceId && lastItem.employeeId && lastItem.durationMinutes) {
        await fetchAvailableDates({
            serviceId: lastItem.serviceId,
            employeeId: lastItem.employeeId,
            durationMinutes: lastItem.durationMinutes,
        });
    }

    // reload available slots
    if (
        lastItem.serviceId &&
        lastItem.employeeId &&
        lastItem.durationMinutes &&
        lastItem.date
    ) {
        await fetchAvailableSlots({
            serviceId: lastItem.serviceId,
            employeeId: lastItem.employeeId,
            durationMinutes: lastItem.durationMinutes,
            date: lastItem.date,
        });
    }

    setStep(4);
};

    return (
        <>
            {step === 1 && (
                <StepSelectService
                    services={servicesData}
                    currentItem={currentItem}
                    savedItems={savedItems}
                    onSelectService={handleSelectService}
                    onContinue={() => setStep(2)}
                />
            )}

            {step === 2 && (
                <>
                    {loadingServiceDetails ? (
                        <div className="container py-5">
                            <div className="alert alert-info rounded-4">Loading durations...</div>
                        </div>
                    ) : serviceDetailsError ? (
                        <div className="container py-5">
                            <div className="alert alert-danger rounded-4">{serviceDetailsError}</div>
                        </div>
                    ) : (
                        <StepSelectDuration
                            currentItem={currentItem}
                            durationOptions={durationOptions}
                            savedItems={savedItems}
                            onSelectDuration={handleSelectDuration}
                            onBack={() => setStep(1)}
                            onContinue={() => setStep(3)}
                        />
                    )}
                </>
            )}

            {step === 3 && (
                <>
                    {loadingServiceDetails ? (
                        <div className="container py-5">
                            <div className="alert alert-info rounded-4">Loading employees...</div>
                        </div>
                    ) : serviceDetailsError ? (
                        <div className="container py-5">
                            <div className="alert alert-danger rounded-4">{serviceDetailsError}</div>
                        </div>
                    ) : (
                        <StepSelectEmployee
                            currentItem={currentItem}
                            employeeOptions={employeeOptions}
                            savedItems={savedItems}
                            onSelectEmployee={handleSelectEmployee}
                            onBack={() => setStep(2)}
                            onContinue={() => setStep(4)}
                        />
                    )}
                </>
            )}

            {step === 4 && (
                <>
                    {availabilityError && (
                        <div className="container pt-4">
                            <div className="alert alert-danger rounded-4 mb-0">
                                {availabilityError}
                            </div>
                        </div>
                    )}

                    <StepSelectDateTime
                        currentItem={currentItem}
                        savedItems={savedItems}
                        availableDates={availableDates}
                        availableSlots={availableSlots}
                        loadingDates={loadingDates}
                        loadingSlots={loadingSlots}
                        onDateChange={handleDateChange}
                        onSelectSlot={handleSelectSlot}
                        onBack={() => setStep(3)}
                        onContinue={handleFinishCurrentService}
                    />
                </>
            )}

            {step === 5 && (
    <AuthStep
        savedItems={savedItems}
        authMode={authMode}
        setAuthMode={setAuthMode}
        authData={authData}
        setAuthData={setAuthData}
        onBack={handleBackToLastService}
        onAddMoreService={handleAddMoreService}
        onContinue={handleAuthContinue}
        onRemoveItem={handleRemoveItem}
        loading={authLoading}
        error={authError}
    />
)}
{step === 6 && (
    <CheckoutStep
        businessSlug={businessSlug}
        savedItems={savedItems}
        authData={authData}
        authenticatedUser={authenticatedUser}
        onBack={() => setStep(5)}
        onRemoveItem={handleRemoveItem}
        onBookingSuccess={(result) => {
            setBookingResponse(result);
            setStep(7);
        }}
    />
)}
{step === 7 && (
    <BookingSuccessStep
        bookingResponse={bookingResponse}
        onBookAnother={() => {
            setSavedItems([]);
            setCurrentItem(emptyServiceItem(Date.now()));
            setSelectedServiceDetails(null);
            setAvailableDates([]);
            setAvailableSlots([]);
            setAvailabilityError('');
            setAuthError('');
            setBookingResponse(null);
            setStep(1);
        }}
    />
)}
        </>
    );
}