import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ServiceApiService, Service, Appointment } from '../../../core/appointment';
import { Step1Services } from './step-1-services/step-1-services';
import { Step2DateTime } from './step-2-datetime/step-2-datetime';
import { Step3Summary } from './step-3-summary/step-3-summary';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, Step1Services, Step2DateTime, Step3Summary],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  services: Service[] = [];
  selectedServices: number[] = [];
  selectedDate: string = '';
  selectedTime: string = '';
  currentStep: 1 | 2 | 3 = 1;
  loading = false;
  error = '';
  success = '';
  minDateTime: string = '';

  constructor(
    private serviceApi: ServiceApiService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadServices();
    this.setMinDateTime();
  }

  /**
   * Carrega lista de serviços disponíveis
   */
  private loadServices(): void {
    this.serviceApi.list().subscribe({
      next: (data) => {
        this.services = data;
        this.cdr.detectChanges();
        console.log('Services loaded:', this.services);
      },
      error: (err) => {
        this.error = 'Erro ao carregar serviços';
        console.error(err);
      }
    });
  }

  /**
   * Define a data/hora mínima para o agendamento (hoje + 1 dia)
   */
  private setMinDateTime(): void {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const year = tomorrow.getFullYear();
    const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const day = String(tomorrow.getDate()).padStart(2, '0');
    const hour = String(tomorrow.getHours()).padStart(2, '0');
    const minute = String(tomorrow.getMinutes()).padStart(2, '0');
    this.minDateTime = `${year}-${month}-${day}T${hour}:${minute}`;
  }

  /**
   * Alterna seleção de serviço
   */
  toggleService(serviceId: number): void {
    const index = this.selectedServices.indexOf(serviceId);
    this.error = '';

    if (index > -1) {
      this.selectedServices.splice(index, 1);
    } else {
      this.selectedServices.push(serviceId);
    }
  }

  /**
   * Avança para o próximo step
   */
  goToNextStep(): void {
    if (this.currentStep < 3) {
      this.currentStep = (this.currentStep + 1) as 1 | 2 | 3;
      this.error = '';
    }
  }

  /**
   * Volta para o step anterior
   */
  goToPrevStep(): void {
    if (this.currentStep > 1) {
      this.currentStep = (this.currentStep - 1) as 1 | 2 | 3;
      this.error = '';
    }
  }

  /**
   * Recebe evento de seleção de data/hora do step 2
   */
  onDateTimeSelected(data: { date: string; time: string }): void {
    this.selectedDate = data.date;
    this.selectedTime = data.time;
    this.goToNextStep();
  }

  /**
   * Recebe evento de agendamento criado do step 3
   */
  onAppointmentCreated(appointment: Appointment): void {
    this.success = 'Agendamento realizado com sucesso!';
    setTimeout(() => {
      this.router.navigate(['/client/appointments']);
    }, 1500);
  }

  /**
   * Formata duração em minutos para horas e minutos
   */
  formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
      return `${hours}h ${mins}min`;
    }
    return `${mins}min`;
  }

  /**
   * Calcula progresso dos steps (0-100%)
   */
  getProgress(): number {
    return (this.currentStep / 3) * 100;
  }
}

