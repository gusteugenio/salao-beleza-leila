import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AppointmentApiService, Appointment, AppointmentService } from '../../../core/appointment';

@Component({
  selector: 'app-appointments',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './appointments.html',
  styleUrl: './appointments.css',
})
export class Appointments implements OnInit {
  appointments: Appointment[] = [];
  filteredAppointments: Appointment[] = [];
  loading = true;
  error = '';
  success = '';
  
  // Filtros
  filterStatus: string = '';
  filterDateFrom: string = '';
  filterDateTo: string = '';
  searchText: string = '';

  // Modal de edição
  showEditModal = false;
  editingAppointment: Appointment | null = null;
  editScheduledAt: string = '';
  editError = '';
  editLoading = false;

  // Modal de detalhes
  showDetailsModal = false;
  selectedAppointment: Appointment | null = null;

  // Modal de confirmação de cancelamento
  showCancelConfirmModal = false;
  appointmentToCancel: Appointment | null = null;

  statuses = [
    { value: '', label: 'Todos os Status' },
    { value: 'Pendente', label: 'Pendente' },
    { value: 'Agendado', label: 'Agendado' },
    { value: 'Finalizado', label: 'Finalizado' },
    { value: 'Cancelado', label: 'Cancelado' }
  ];

  constructor(
    private appointmentApi: AppointmentApiService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadAppointments();
  }

  /**
   * Carrega agendamentos do servidor
   */
  private loadAppointments(): void {
    this.loading = true;
    this.error = '';
    
    this.appointmentApi.list().subscribe({
      next: (data) => {
        this.appointments = data;
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

    // Filtro por texto (nome do serviço ou data)
    if (this.searchText) {
      const search = this.searchText.toLowerCase();
      filtered = filtered.filter(a => {
        const services = a.services.map(s => s.name).join(' ').toLowerCase();
        const date = this.formatDate(a.scheduled_at);
        return services.includes(search) || date.includes(search);
      });
    }

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
   * Calcula preço total do agendamento
   */
  getTotalPrice(appointment: Appointment): number {
    return appointment.services.reduce((sum, service) => sum + parseFloat(service.price as unknown as string), 0);
  }

  /**
   * Retorna classe CSS para status
   */
  getStatusClass(status: string): string {
    const statusMap: { [key: string]: string } = {
      'Pendente': 'status-pending',
      'Agendado': 'status-confirmed',
      'Finalizado': 'status-completed',
      'Cancelado': 'status-cancelled'
    };
    return statusMap[status] || '';
  }

  /**
   * Verifica se é possível editar o agendamento (mais de 2 dias)
   */
  canEdit(appointment: Appointment): boolean {
    const now = new Date();
    const scheduled = new Date(appointment.scheduled_at);
    const daysUntil = (scheduled.getTime() - now.getTime()) / (1000 * 60 * 60 * 24);
    return daysUntil > 2 && appointment.status !== 'Cancelado' && appointment.status !== 'Finalizado';
  }

  /**
   * Verifica se e possivel cancelar o agendamento (mais de 2 dias)
   */
  canCancel(appointment: Appointment): boolean {
    const now = new Date();
    const scheduled = new Date(appointment.scheduled_at);
    const daysUntil = (scheduled.getTime() - now.getTime()) / (1000 * 60 * 60 * 24);
    return daysUntil > 2 && appointment.status !== 'Cancelado' && appointment.status !== 'Finalizado';
  }

  /**
   * Abre modal de edição
   */
  openEditModal(appointment: Appointment): void {
    if (!this.canEdit(appointment)) {
      this.error = 'Não é possível editar este agendamento';
      return;
    }
    
    this.editingAppointment = appointment;
    this.editScheduledAt = this.formatDateForInput(appointment.scheduled_at);
    this.editError = '';
    this.showEditModal = true;
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
    
    // Validacao: hora deve ser valida
    if (isNaN(scheduled.getTime())) {
      this.editError = 'Data e hora invalidas';
      return;
    }

    const now = new Date();
    if (scheduled <= now) {
      this.editError = 'A data e hora deve ser no futuro';
      return;
    }
    const year = scheduled.getFullYear();
    const month = String(scheduled.getMonth() + 1).padStart(2, '0');
    const day = String(scheduled.getDate()).padStart(2, '0');
    const hour = String(scheduled.getHours()).padStart(2, '0');
    const minute = String(scheduled.getMinutes()).padStart(2, '0');
    const scheduledAtFormatted = `${year}-${month}-${day} ${hour}:${minute}:00`;

    this.editLoading = true;
    this.appointmentApi.update(this.editingAppointment.id, {
      scheduled_at: scheduledAtFormatted
    }).subscribe({
      next: () => {
        this.editLoading = false;
        this.success = 'Agendamento atualizado com sucesso!';
        this.showEditModal = false;
        this.loadAppointments();
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.editLoading = false;
        this.editError = err.error?.message || 'Erro ao atualizar agendamento';
        if (err.error?.errors) {
          const errors = err.error.errors;
          const firstError = Object.values(errors)[0] as string[];
          if (firstError?.[0]) {
            this.editError = firstError[0];
          }
        }
        this.cdr.markForCheck();
      }
    });
  }

  /**
   * Fecha modal de edição
   */
  closeEditModal(): void {
    this.showEditModal = false;
    this.editingAppointment = null;
    this.editError = '';
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
   * Abre confirmação de cancelamento
   */
  openCancelConfirm(appointment: Appointment): void {
    this.appointmentToCancel = appointment;
    this.showCancelConfirmModal = true;
  }

  /**
   * Cancela agendamento
   */
  confirmCancel(): void {
    if (!this.appointmentToCancel) return;

    this.editLoading = true;
    this.appointmentApi.cancel(this.appointmentToCancel.id).subscribe({
      next: () => {
        this.editLoading = false;
        this.success = 'Agendamento cancelado com sucesso!';
        this.showCancelConfirmModal = false;
        this.loadAppointments();
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.editLoading = false;
        this.error = err.error?.message || 'Erro ao cancelar agendamento';
        this.cdr.markForCheck();
      }
    });
  }

  /**
   * Fecha modal de confirmação de cancelamento
   */
  closeCancelConfirm(): void {
    this.showCancelConfirmModal = false;
    this.appointmentToCancel = null;
  }
}
