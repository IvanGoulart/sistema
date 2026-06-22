<x-mail::message>
# Agendamento Cancelado

Olá {{ $schedule->client->name }},

Informamos que seu agendamento foi cancelado.

<x-mail::panel>
**Detalhes do Agendamento Cancelado:**

- **Serviço:** {{ $serviceName }}
- **Profissional:** {{ $employeeName }}
- **Data:** {{ $day }}
- **Horário:** {{ $hour }}
</x-mail::panel>

Se deseja remarcar, acesse nosso portal e faça um novo agendamento.

Obrigado,<br>
**Salão Fácil**
</x-mail::message>
