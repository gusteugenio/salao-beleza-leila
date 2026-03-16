import { Component, Input, Output, EventEmitter, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Service } from '../../../../core/appointment';

@Component({
  selector: 'app-step-1-services',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './step-1-services.html',
  styleUrl: './step-1-services.css',
})
export class Step1Services implements OnChanges {
  @Input() services: Service[] = [];
  @Input() selectedServices: number[] = [];
  @Output() serviceToggled = new EventEmitter<number>();
  @Output() nextStep = new EventEmitter<void>();

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['services']) {
      console.log('Services received in step-1:', this.services);
    }
  }

  toggleService(serviceId: number): void {
    this.serviceToggled.emit(serviceId);
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

  isServiceSelected(serviceId: number): boolean {
    return this.selectedServices.includes(serviceId);
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

  canProceed(): boolean {
    return this.selectedServices.length > 0;
  }

  handleNextStep(): void {
    if (this.canProceed()) {
      this.nextStep.emit();
    }
  }
}
