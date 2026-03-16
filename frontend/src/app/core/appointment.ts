import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Service {
  id: number;
  name: string;
  duration: number;
  price: number;
}

export interface AppointmentService {
  id: number;
  name: string;
  duration: number;
  price: number;
  pivot?: {
    status: 'Pendente' | 'Agendado' | 'Finalizado' | 'Cancelado';
    start_at: string;
    end_at: string;
  };
}

export interface Appointment {
  id: number;
  user_id: number;
  scheduled_at: string;
  status: 'Pendente' | 'Agendado' | 'Finalizado' | 'Cancelado';
  services: AppointmentService[];
  created_at: string;
  updated_at: string;
}

export interface ValidationResponse {
  status: 'OK' | 'ASK_UNIFY';
  existing_appointment?: Appointment;
}

@Injectable({
  providedIn: 'root',
})
export class AppointmentApiService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Valida a criação de um agendamento
   */
  validateCreation(scheduledAt: string, services: number[]): Observable<ValidationResponse> {
    return this.http.post<ValidationResponse>(
      `${this.apiUrl}/appointments/check`,
      { scheduled_at: scheduledAt, services }
    );
  }

  /**
   * Cria um novo agendamento
   */
  create(scheduledAt: string, services: number[]): Observable<Appointment> {
    return this.http.post<Appointment>(
      `${this.apiUrl}/appointments`,
      { scheduled_at: scheduledAt, services }
    );
  }

  /**
   * Adiciona serviços a um agendamento existente
   */
  addServices(appointmentId: number, scheduledAt: string, services: number[]): Observable<Appointment> {
    return this.http.post<Appointment>(
      `${this.apiUrl}/appointments/${appointmentId}/add-services`,
      { scheduled_at: scheduledAt, services }
    );
  }

  /**
   * Lista agendamentos do usuário
   */
  list(filters?: any): Observable<Appointment[]> {
    let params = new HttpParams();
    if (filters) {
      Object.keys(filters).forEach(key => {
        if (filters[key]) {
          params = params.set(key, filters[key]);
        }
      });
    }
    return this.http.get<Appointment[]>(`${this.apiUrl}/appointments`, { params });
  }

  /**
   * Obtém detalhes de um agendamento
   */
  show(appointmentId: number): Observable<Appointment> {
    return this.http.get<Appointment>(`${this.apiUrl}/appointments/${appointmentId}`);
  }

  /**
   * Atualiza um agendamento
   */
  update(appointmentId: number, data: any): Observable<Appointment> {
    return this.http.patch<Appointment>(
      `${this.apiUrl}/appointments/${appointmentId}`,
      data
    );
  }

  /**
   * Remove um serviço de um agendamento
   */
  removeService(appointmentId: number, serviceId: number): Observable<Appointment> {
    return this.http.patch<Appointment>(
      `${this.apiUrl}/appointments/${appointmentId}/removeService/${serviceId}`,
      {}
    );
  }

  /**
   * Atualiza o status de um serviço
   */
  updateServiceStatus(appointmentId: number, serviceId: number, status: string): Observable<Appointment> {
    return this.http.patch<Appointment>(
      `${this.apiUrl}/appointments/${appointmentId}/services/${serviceId}/status`,
      { status }
    );
  }

  /**
   * Cancela um agendamento
   */
  cancel(appointmentId: number): Observable<Appointment> {
    return this.http.patch<Appointment>(
      `${this.apiUrl}/appointments/${appointmentId}/cancel`,
      {}
    );
  }

  /**
   * Obtém horários disponíveis para uma data e duração
   */
  getAvailableTimes(date: string, durationMinutes: number): Observable<string[]> {
    const params = new HttpParams()
      .set('date', date)
      .set('duration', durationMinutes.toString());
    return this.http.get<string[]>(
      `${this.apiUrl}/appointments/available-times`,
      { params }
    );
  }
}

@Injectable({
  providedIn: 'root',
})
export class ServiceApiService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Lista todos os serviços disponíveis
   */
  list(): Observable<Service[]> {
    return this.http.get<Service[]>(`${this.apiUrl}/services`);
  }

  /**
   * Obtém detalhes de um serviço
   */
  show(serviceId: number): Observable<Service> {
    return this.http.get<Service>(`${this.apiUrl}/services/${serviceId}`);
  }
}
