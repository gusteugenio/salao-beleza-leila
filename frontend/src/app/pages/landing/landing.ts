import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './landing.html',
  styleUrl: './landing.css'
})
export class Landing implements OnInit {
  currentSlide = 0;
  
  services = [
    {
      name: 'Corte de Cabelo',
      duration: '60 min',
      price: 'R$ 50,00',
      image: '/images/service-corte.jpg'
    },
    {
      name: 'Manicure',
      duration: '45 min',
      price: 'R$ 30,00',
      image: '/images/service-manicure.jpg'
    },
    {
      name: 'Pedicure',
      duration: '45 min',
      price: 'R$ 35,00',
      image: '/images/service-pedicure.jpg'
    },
    {
      name: 'Coloração',
      duration: '90 min',
      price: 'R$ 80,00',
      image: '/images/service-coloracao.jpg'
    },
    {
      name: 'Alisamento',
      duration: '120 min',
      price: 'R$ 120,00',
      image: '/images/service-alisamento.jpg'
    },
    {
      name: 'Escova Progressiva',
      duration: '90 min',
      price: 'R$ 100,00',
      image: '/images/service-escova.jpg'
    }
  ];

  testimonials = [
    {
      name: 'Maria Silva',
      text: 'Adorei o sistema! Muito fácil marcar meus agendamentos online.',
      rating: 5
    },
    {
      name: 'Ana Costa',
      text: 'Leila está sempre atenta aos detalhes. Recomendo!',
      rating: 5
    },
    {
      name: 'Juliana Santos',
      text: 'Serviço de qualidade e ambiente aconchegante.',
      rating: 5
    }
  ];

  constructor(
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.startAutoSlide();
  }

  startAutoSlide(): void {
    setInterval(() => {
      this.nextSlide();
    }, 5000);
  }

  nextSlide(): void {
    this.currentSlide = (this.currentSlide + 1) % this.services.length;
    this.cdr.markForCheck();
  }

  prevSlide(): void {
    this.currentSlide = (this.currentSlide - 1 + this.services.length) % this.services.length;
    this.cdr.markForCheck();
  }

  goToSlide(index: number): void {
    this.currentSlide = index;
    this.cdr.markForCheck();
  }

  navigateToLogin(): void {
    this.router.navigate(['/auth/login']);
  }

  navigateToBooking(): void {
    this.router.navigate(['/client/home']);
  }

  getRating(rating: number): string[] {
    return Array(rating).fill('⭐');
  }
}
