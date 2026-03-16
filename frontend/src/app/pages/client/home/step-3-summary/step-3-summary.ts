import { Component, Input, Output, EventEmitter, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AppointmentApiService, Service, Appointment } from '../../../../core/appointment';

@Component({
  selector: 'app-step-3-summary',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './step-3-summary.html',
  styleUrl: './step-3-summary.css',
})
export class Step3Summary {
  @Input() services: Service[] = [];
  @Input() selectedServices: number[] = [];
  @Input() selectedDate: string = '';
  @Input() selectedTime: string = '';
  @Output() prevStep = new EventEmitter<void>();
  @Output() appointmentCreated = new EventEmitter<Appointment>();

  loading = false;
  error = '';
  showUnifyModal = false;
  existingAppointment: Appointment | null = null;
  isValidating = false;

  constructor(
    private appointmentApi: AppointmentApiService,
    private cdr: ChangeDetectorRef
  ) {}

  getSelectedServiceDetails(): Service[] {
    return this.selectedServices
      .map(id => this.services.find(s => s.id === id))
      .filter((service): service is Service => service !== undefined);
  }

  getTotalDuration(): number {
    return this.selectedServices.reduce((total, serviceId) => {
      const service = this.services.find(s => s.id === serviceId);
      return total + (service?.duration || 0);
    }, 0);
  }

  getTotalPrice(): number {
    return this.selectedServices.reduce((total, serviceId) => {
      const service = this.services.find(s => s.id === serviceId);
      return total + (service ? parseFloat(service.price as unknown as string) : 0);
    }, 0);
  }

  formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
      return `${hours}h ${mins}min`;
    }
    return `${mins}min`;
  }

  formatPrice(price: number): string {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(price);
  }

  formatDateTime(): string {
    const [year, month, day] = this.selectedDate.split('-');
    const date = new Date(`${year}-${month}-${day}`);
    const dateStr = date.toLocaleDateString('pt-BR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    const timeStr = this.selectedTime.slice(0, 5);
    return `${dateStr} às ${timeStr}`;
  }

  // Calcola horários sequenciais dos serviços selecionados
  getServiceTimeline(): Array<{ service: Service; start: string; end: string }> {
    const services = this.getSelectedServiceDetails();
    const timeline = [];
    let currentTime = this.parseTime(this.selectedTime);

    for (const service of services) {
      const endTime = new Date(currentTime);
      endTime.setMinutes(endTime.getMinutes() + service.duration);

      timeline.push({
        service,
        start: this.formatTimeOnly(currentTime),
        end: this.formatTimeOnly(endTime)
      });

      currentTime = endTime;
    }

    return timeline;
  }

  private parseTime(timeStr: string): Date {
    const [hours, minutes] = timeStr.split(':').map(Number);
    const date = new Date(`${this.selectedDate}T${this.selectedTime}`);
    date.setHours(hours, minutes);
    return date;
  }

  private formatTimeOnly(date: Date): string {
    return date.toLocaleTimeString('pt-BR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  handlePrevStep(): void {
    this.prevStep.emit();
  }

  validateAndCreate(): void {
    this.error = '';
    this.isValidating = true;

    // Format datetime for API
    const scheduled = new Date(`${this.selectedDate}T${this.selectedTime}`);
    const year = scheduled.getFullYear();
    const month = String(scheduled.getMonth() + 1).padStart(2, '0');
    const day = String(scheduled.getDate()).padStart(2, '0');
    const hour = String(scheduled.getHours()).padStart(2, '0');
    const minute = String(scheduled.getMinutes()).padStart(2, '0');
    const scheduledAtFormatted = `${year}-${month}-${day} ${hour}:${minute}:00`;

    this.appointmentApi.validateCreation(scheduledAtFormatted, this.selectedServices).subscribe({
      next: (response: any) => {
        this.isValidating = false;
        if (response.status === 'OK') {
          this.createAppointment();
        } else if (response.status === 'ASK_UNIFY') {
          this.existingAppointment = response.existing_appointment || null;
          this.showUnifyModal = true;
        }
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.isValidating = false;
        this.error = err.error?.message || 'Erro ao validar agendamento';
        if (err.error?.errors) {
          const errors = err.error.errors;
          const firstError = Object.values(errors)[0] as string[];
          if (firstError?.[0]) {
            this.error = firstError[0];
          }
        }
        this.cdr.markForCheck();
      }
    });
  }

  private createAppointment(): void {
    const scheduled = new Date(`${this.selectedDate}T${this.selectedTime}`);
    const year = scheduled.getFullYear();
    const month = String(scheduled.getMonth() + 1).padStart(2, '0');
    const day = String(scheduled.getDate()).padStart(2, '0');
    const hour = String(scheduled.getHours()).padStart(2, '0');
    const minute = String(scheduled.getMinutes()).padStart(2, '0');
    const scheduledAtFormatted = `${year}-${month}-${day} ${hour}:${minute}:00`;

    this.loading = true;
    this.appointmentApi.create(scheduledAtFormatted, this.selectedServices).subscribe({
      next: (appointment) => {
        this.loading = false;
        this.appointmentCreated.emit(appointment);
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao criar agendamento';
        if (err.error?.errors) {
          const errors = err.error.errors;
          const firstError = Object.values(errors)[0] as string[];
          if (firstError?.[0]) {
            this.error = firstError[0];
          }
        }
        this.cdr.markForCheck();
      }
    });
  }

  addServicesToExisting(): void {
    if (!this.existingAppointment) {
      return;
    }

    const scheduled = new Date(`${this.selectedDate}T${this.selectedTime}`);
    const year = scheduled.getFullYear();
    const month = String(scheduled.getMonth() + 1).padStart(2, '0');
    const day = String(scheduled.getDate()).padStart(2, '0');
    const hour = String(scheduled.getHours()).padStart(2, '0');
    const minute = String(scheduled.getMinutes()).padStart(2, '0');
    const scheduledAtFormatted = `${year}-${month}-${day} ${hour}:${minute}:00`;

    this.loading = true;
    this.appointmentApi.addServices(
      this.existingAppointment.id,
      scheduledAtFormatted,
      this.selectedServices
    ).subscribe({
      next: (appointment) => {
        this.loading = false;
        this.showUnifyModal = false;
        this.appointmentCreated.emit(appointment);
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao adicionar serviços';
        this.cdr.markForCheck();
      }
    });
  }

  closeUnifyModal(): void {
    this.showUnifyModal = false;
  }
}
