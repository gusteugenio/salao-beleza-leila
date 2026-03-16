import { Component, Input, Output, EventEmitter, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppointmentApiService, Service } from '../../../../core/appointment';

interface TimeSlot {
  time: string;
  available: boolean;
}

@Component({
  selector: 'app-step-2-datetime',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './step-2-datetime.html',
  styleUrl: './step-2-datetime.css',
})
export class Step2DateTime implements OnInit {
  @Input() services: Service[] = [];
  @Input() selectedServices: number[] = [];
  @Input() minDateTime: string = '';
  @Output() prevStep = new EventEmitter<void>();
  @Output() nextStep = new EventEmitter<{ date: string; time: string }>();

  selectedDate: string = '';
  selectedTime: string = '';
  timeSlots: TimeSlot[] = [];
  loading = false;
  error = '';

  constructor(
    private appointmentApi: AppointmentApiService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.setMinDate();
  }

  private setMinDate(): void {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    this.selectedDate = `${year}-${month}-${day}`;
    
    this.loadAvailableTimes();
  }

  getTotalDuration(): number {
    return this.selectedServices.reduce((total, serviceId) => {
      const service = this.services.find(s => s.id === serviceId);
      return total + (service?.duration || 0);
    }, 0);
  }

  onDateChange(): void {
    this.selectedTime = '';
    this.loadAvailableTimes();
  }

  private loadAvailableTimes(): void {
    if (!this.selectedDate) {
      return;
    }

    this.loading = true;
    this.error = '';
    this.timeSlots = [];

    const duration = this.getTotalDuration();

    this.appointmentApi.getAvailableTimes(this.selectedDate, duration).subscribe({
      next: (times: string[]) => {
        this.timeSlots = times.map(time => ({
          time,
          available: true
        }));
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.message || 'Erro ao carregar horários disponíveis';
        this.cdr.markForCheck();
      }
    });
  }

  selectTime(time: string): void {
    if (this.selectedTime === time) {
      this.selectedTime = '';
    } else {
      this.selectedTime = time;
    }
  }

  isTimeSelected(time: string): boolean {
    return this.selectedTime === time;
  }

  formatTime(time: string): string {
    return time.slice(0, 5); // HH:mm
  }

  formatDate(dateString: string): string {
    const [year, month, day] = dateString.split('-');
    const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
    return date.toLocaleDateString('pt-BR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  canProceed(): boolean {
    return this.selectedDate !== '' && this.selectedTime !== '';
  }

  formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
      return `${hours}h ${mins}min`;
    }
    return `${mins}min`;
  }

  handlePrevStep(): void {
    this.prevStep.emit();
  }

  handleNextStep(): void {
    if (this.canProceed()) {
      this.nextStep.emit({
        date: this.selectedDate,
        time: this.selectedTime
      });
    }
  }
}
