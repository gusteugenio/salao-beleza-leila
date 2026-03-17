import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { AppointmentApiService, Appointment } from '../../../core/appointment';

@Component({
  selector: 'app-appointments',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './appointments.html',
  styleUrl: './appointments.css',
})
export class Appointments implements OnInit {
  private apiUrl = 'http://localhost:8000/api';

  appointments: Appointment[] = [];
  filteredAppointments: Appointment[] = [];
  clients: any[] = [];
  loading = true;
  error = '';
  success = '';

  // Filtros
  filterStatus: string = '';
  filterDateFrom: string = '';
  filterDateTo: string = '';
  filterClientId: string = '';

  // Modal de edição
  showEditModal = false;
  editingAppointment: Appointment | null = null;
  editScheduledAt: string = '';
  editError = '';
  editLoading = false;

  // Modal de detalhes
  showDetailsModal = false;
  selectedAppointment: Appointment | null = null;

  // Modal de status de serviço
  showStatusModal = false;
  selectedService: any = null;
  selectedServiceStatus: string = '';

  statuses = [
    { value: '', label: 'Todos os Status' },
    { value: 'Pendente', label: 'Pendente' },
    { value: 'Confirmado', label: 'Confirmado' },
    { value: 'Cancelado', label: 'Cancelado' }
  ];

  serviceStatuses = ['Pendente', 'Finalizado', 'Cancelado'];

  constructor(
    private http: HttpClient,
    private appointmentApi: AppointmentApiService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadAppointments();
  }

  /**
   * Carrega agendamentos (admin vê todos)
   */
  private loadAppointments(): void {
    this.loading = true;
    this.error = '';

    this.http.get<Appointment[]>(`${this.apiUrl}/appointments/all`)
      .subscribe({
        next: (data) => {
          this.appointments = data;
          this.extractClients();
          this.applyFilters();
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (err) => {
          this.loading = false;
          this.error = 'Erro ao carregar agendamentos';
          console.error(err);
          this.cdr.markForCheck();
        }
      });
  }

  private extractClients(): void {
    const clientMap = new Map();
    this.appointments.forEach(appointment => {
      if (appointment.user && !clientMap.has(appointment.user.id)) {
        clientMap.set(appointment.user.id, appointment.user);
      }
    });
    this.clients = Array.from(clientMap.values());
  }

  /**
   * Aplica filtros à lista de agendamentos
   */
  applyFilters(): void {
    let filtered = [...this.appointments];

    // Filtro por status
    if (this.filterStatus) {
      filtered = filtered.filter(a => a.status === this.filterStatus);
    }

    // Filtro por data
    if (this.filterDateFrom) {
      const fromDate = new Date(this.filterDateFrom + 'T00:00:00');
      filtered = filtered.filter(a => new Date(a.scheduled_at) >= fromDate);
    }

    if (this.filterDateTo) {
      const toDate = new Date(this.filterDateTo + 'T23:59:59.999');
      filtered = filtered.filter(a => new Date(a.scheduled_at) <= toDate);
    }

    // Filtro por cliente
    if (this.filterClientId) {
      filtered = filtered.filter(a => a.user_id === Number(this.filterClientId));
    }

    // Ordena por data (mais próxima primeiro)
    filtered.sort((a, b) => new Date(a.scheduled_at).getTime() - new Date(b.scheduled_at).getTime());

    this.filteredAppointments = filtered;
  }

  /**
   * Formata data para exibição
   */
  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  }

  /**
   * Formata data para input datetime
   */
  formatDateForInput(dateString: string): string {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hour = String(date.getHours()).padStart(2, '0');
    const minute = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hour}:${minute}`;
  }

  /**
   * Formata preço
   */
  formatPrice(price: number): string {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(price);
  }

  /**
   * Formata horário do serviço em HH:mm
   */
  formatServiceTime(dateTimeString: string): string {
    const date = new Date(dateTimeString);
    return date.toLocaleTimeString('pt-BR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  /**
   * Retorna agendamento dos serviços formatados
   */
  getServiceSchedule(appointment: Appointment): string {
    return appointment.services.map(s => `${s.name}`).join(', ');
  }

  /**
   * Obtém classe CSS do status
   */
  getStatusClass(status: string): string {
    return status.toLowerCase();
  }

  /**
   * Abre modal de detalhes
   */
  openDetailsModal(appointment: Appointment): void {
    this.selectedAppointment = appointment;
    this.showDetailsModal = true;
  }

  /**
   * Fecha modal de detalhes
   */
  closeDetailsModal(): void {
    this.showDetailsModal = false;
    this.selectedAppointment = null;
  }

  /**
   * Abre modal de edição (sem restrição de 2 dias para admin)
   */
  openEditModal(appointment: Appointment): void {
    this.editingAppointment = appointment;
    this.editScheduledAt = this.formatDateForInput(appointment.scheduled_at);
    this.editError = '';
    this.showEditModal = true;
  }

  /**
   * Fecha modal de edição
   */
  closeEditModal(): void {
    this.showEditModal = false;
    this.editingAppointment = null;
    this.editScheduledAt = '';
    this.editError = '';
  }

  /**
   * Salva edições do agendamento
   */
  saveEdit(): void {
    if (!this.editingAppointment || !this.editScheduledAt) {
      this.editError = 'Preencha a data e hora';
      return;
    }

    const scheduled = new Date(this.editScheduledAt);

    // Validacao: hora deve ser válida
    if (isNaN(scheduled.getTime())) {
      this.editError = 'Data e hora inválidas';
      return;
    }

    const now = new Date();
    if (scheduled <= now) {
      this.editError = 'A data e hora deve ser no futuro';
      return;
    }

    this.editLoading = true;

    // Converte formato: "2026-03-18T18:30" -> "2026-03-18 18:30:00"
    const formattedDateTime = this.editScheduledAt.replace('T', ' ') + ':00';

    // Admin consegue editar direto - sem validação de 2 dias
    this.appointmentApi.update(this.editingAppointment.id, {
      scheduled_at: formattedDateTime
    }).subscribe({
      next: () => {
        this.editLoading = false;
        this.success = 'Agendamento atualizado com sucesso!';
        this.closeEditModal();
        setTimeout(() => {
          this.loadAppointments();
          this.success = '';
        }, 1500);
      },
      error: (err) => {
        this.editLoading = false;
        
        // Trata diferentes tipos de erro
        if (err.error?.errors) {
          // Erros de validação com campos específicos
          const errorMessages = Object.values(err.error.errors)
            .flat()
            .join(' ');
          this.editError = errorMessages || 'Erro ao atualizar agendamento';
        } else if (err.error?.message) {
          this.editError = err.error.message;
        } else {
          this.editError = 'Erro ao atualizar agendamento';
        }
        
        this.cdr.markForCheck();
      }
    });
  }

  /**
   * Abre modal para editar status do serviço
   */
  openStatusModal(service: any, appointment?: Appointment): void {
    this.selectedService = service;
    this.selectedServiceStatus = service.pivot.status;
    // Se appointment foi passado, usa ese; senão usa editingAppointment
    if (appointment) {
      this.editingAppointment = appointment;
    }
    this.showStatusModal = true;
  }

  /**
   * Fecha modal de status
   */
  closeStatusModal(): void {
    this.showStatusModal = false;
    this.selectedService = null;
    this.selectedServiceStatus = '';
  }

  /**
   * Salva novo status do serviço
   */
  saveServiceStatus(): void {
    if (!this.selectedService || !this.editingAppointment) return;

    this.editLoading = true;

    this.appointmentApi.updateServiceStatus(
      this.editingAppointment.id,
      this.selectedService.id,
      this.selectedServiceStatus
    ).subscribe({
      next: () => {
        this.editLoading = false;
        this.success = 'Status atualizado com sucesso!';
        this.closeStatusModal();
        this.cdr.markForCheck();
        setTimeout(() => {
          this.loadAppointments();
          this.success = '';
        }, 1500);
      },
      error: (err) => {
        this.editLoading = false;
        this.error = err.error?.message || 'Erro ao atualizar status';
        this.cdr.markForCheck();
      }
    });
  }

  /**
   * Confirma um agendamento
   */
  confirmAppointment(appointment: Appointment): void {
    this.loading = true;

    this.appointmentApi.confirm(appointment.id).subscribe({
      next: () => {
        this.loading = false;
        this.success = 'Agendamento confirmado com sucesso!';
        setTimeout(() => {
          this.loadAppointments();
          this.success = '';
        }, 1500);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao confirmar agendamento';
      }
    });
  }

  /**
   * Cancela um agendamento
   */
  cancelAppointment(appointment: Appointment): void {
    this.loading = true;

    this.appointmentApi.cancel(appointment.id).subscribe({
      next: () => {
        this.loading = false;
        this.success = 'Agendamento cancelado com sucesso!';
        setTimeout(() => {
          this.loadAppointments();
          this.success = '';
        }, 1500);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao cancelar agendamento';
      }
    });
  }

  /**
   * Calcula total de preços
   */
  getTotalPrice(appointment: Appointment): number {
    return appointment.services.reduce((sum, service) => sum + (Number(service.price) || 0), 0);
  }
}
