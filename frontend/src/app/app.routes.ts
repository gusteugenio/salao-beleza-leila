import { Routes } from '@angular/router';
import { Login } from './pages/auth/login/login';
import { Register } from './pages/auth/register/register';
import { Home as ClientHome } from './pages/client/home/home';
import { Appointments as ClientAppointments } from './pages/client/appointments/appointments';
import { Dashboard } from './pages/admin/dashboard/dashboard';
import { Appointments as AdminAppointments } from './pages/admin/appointments/appointments';

export const routes: Routes = [
  { path: '', redirectTo: '/client/home', pathMatch: 'full' },
  {
    path: 'auth',
    children: [
      { path: 'login', component: Login },
      { path: 'register', component: Register }
    ]
  },
  {
    path: 'client',
    children: [
      { path: 'home', component: ClientHome },
      { path: 'appointments', component: ClientAppointments }
    ]
  },
  {
    path: 'admin',
    children: [
      { path: 'dashboard', component: Dashboard },
      { path: 'appointments', component: AdminAppointments }
    ]
  }
];
