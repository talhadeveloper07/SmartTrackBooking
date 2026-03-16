import './bootstrap';
import React from 'react';
import 'react-datepicker/dist/react-datepicker.css';
import ReactDOM from 'react-dom/client';
import CustomerAppointmentStepForm from './components/CustomerAppointmentStepForm';

const el = document.getElementById('customer-appointment-app');
const businessSlug = el?.dataset?.businessSlug || '';

if (el) {
    ReactDOM.createRoot(el).render(
        <React.StrictMode>
            <CustomerAppointmentStepForm businessSlug={businessSlug} />
        </React.StrictMode>
    );
}