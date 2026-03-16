import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

interface WeeklyPerformance {
  total_revenue: number;
  finished_services: number;
  pending_services: number;
  cancellations: number;
  total_appointments: number;
  start_of_week: string;
  end_of_week: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css',
})
export class Dashboard implements OnInit {
  private apiUrl = 'http://localhost:8000/api';
  
  performance: WeeklyPerformance | null = null;
  currentDate: Date = new Date();
  loading = true;
  error = '';
  
  weekStart: Date = new Date();
  weekEnd: Date = new Date();

  constructor(
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadWeeklyPerformance();
  }

  /**
   * Verifica se está na semana atual
   */
  isCurrentWeek(): boolean {
    const today = new Date();
    return this.currentDate.toDateString() === today.toDateString();
  }

  /**
   * Formata número com validação
   */
  formatNumber(value: number | undefined | null): string {
    if (!value || isNaN(value)) return '0';
    return Math.round(value * 10) / 10 + '';
  }

  /**
   * Calcula média de serviços por agendamento
   */
  getServicesPerAppointment(): number {
    if (!this.performance || !this.performance.total_appointments) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    const average = total / (this.performance.total_appointments || 1);
    return isNaN(average) ? 0 : average;
  }

  /**
   * Calcula ticket por serviço
   */
  getTicketPerService(): number {
    if (!this.performance) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    if (total === 0) return 0;
    const ticket = (this.performance.total_revenue || 0) / total;
    return isNaN(ticket) ? 0 : ticket;
  }

  /**
   * Retorna total de serviços com validação
   */
  getTotalServices(): number {
    if (!this.performance) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    return isNaN(total) ? 0 : total;
  }

  /**
   * Retorna total de agendamentos com validação
   */
  getTotalAppointments(): number {
    if (!this.performance) return 0;
    const total = this.performance.total_appointments || 0;
    return isNaN(total) ? 0 : total;
  }

  /**
   * Verifica se há dados válidos
   */
  hasValidData(): boolean {
    return !!this.performance;
  }

  /**
   * Calcula início da semana (segunda-feira)
   */
  private getWeekStart(date: Date): Date {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
  }

  /**
   * Carrega dados de desempenho semanal
   */
  private loadWeeklyPerformance(): void {
    this.loading = true;
    this.error = '';
    
    const dateStr = this.currentDate.toISOString().split('T')[0];
    const weekStart = this.getWeekStart(new Date(this.currentDate));
    this.weekStart = weekStart;
    
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    this.weekEnd = weekEnd;
    
    this.http.get<WeeklyPerformance>(`${this.apiUrl}/dashboard/weekly-performance?date=${dateStr}`)
      .subscribe({
        next: (data) => {
          this.performance = data;
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (err) => {
          this.loading = false;
          this.error = 'Erro ao carregar desempenho da semana';
          console.error(err);
          this.cdr.markForCheck();
        }
      });
  }

  /**
   * Vai para semana anterior
   */
  goToPreviousWeek(): void {
    this.currentDate.setDate(this.currentDate.getDate() - 7);
    this.currentDate = new Date(this.currentDate);
    this.loadWeeklyPerformance();
  }

  /**
   * Vai para próxima semana
   */
  goToNextWeek(): void {
    this.currentDate.setDate(this.currentDate.getDate() + 7);
    this.currentDate = new Date(this.currentDate);
    this.loadWeeklyPerformance();
  }

  /**
   * Volta para semana atual
   */
  goToCurrentWeek(): void {
    this.currentDate = new Date();
    this.loadWeeklyPerformance();
  }

  /**
   * Formata data
   */
  formatDate(date: Date): string {
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    }).format(date);
  }

  /**
   * Formata data para range
   */
  formatDateRange(): string {
    const start = new Intl.DateTimeFormat('pt-BR', {
      day: 'numeric',
      month: 'short'
    }).format(this.weekStart);
    
    const end = new Intl.DateTimeFormat('pt-BR', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    }).format(this.weekEnd);
    
    return `${start} a ${end}`;
  }

  /**
   * Formata moeda
   */
  formatCurrency(value: number | null | undefined): string {
    if (!value || isNaN(value)) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  }

  /**
   * Calcula percentual de cancelamento
   */
  getCancellationRate(): number {
    if (!this.performance) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    if (total === 0) return 0;
    const rate = ((this.performance.cancellations || 0) / total) * 100;
    return isNaN(rate) ? 0 : rate;
  }

  /**
   * Calcula percentual de conclusão
   */
  getCompletionRate(): number {
    if (!this.performance) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    if (total === 0) return 0;
    const rate = ((this.performance.finished_services || 0) / total) * 100;
    return isNaN(rate) ? 0 : rate;
  }

  /**
   * Calcula percentual de serviços pendentes
   */
  getPendingRate(): number {
    if (!this.performance) return 0;
    const total = (this.performance.finished_services || 0) + (this.performance.pending_services || 0) + (this.performance.cancellations || 0);
    if (total === 0) return 0;
    const rate = ((this.performance.pending_services || 0) / total) * 100;
    return isNaN(rate) ? 0 : rate;
  }

  /**
   * Retorna serviços pendentes com validação
   */
  getPendingServices(): number {
    if (!this.performance) return 0;
    const total = this.performance.pending_services || 0;
    return isNaN(total) ? 0 : total;
  }

  /**
   * Ticket médio
   */
  getAverageTicket(): number {
    if (!this.performance || !this.performance.total_appointments) return 0;
    const average = (this.performance.total_revenue || 0) / (this.performance.total_appointments || 1);
    return isNaN(average) ? 0 : average;
  }
}
