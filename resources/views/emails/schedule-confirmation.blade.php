<x-mail::message>
# Agendamento Confirmado! 🎉

Olá {{ $schedule->client->name }},

Seu agendamento foi confirmado com sucesso!

<x-mail::panel>
**Detalhes do Agendamento:**

- **Serviço:** {{ $serviceName }}
- **Profissional:** {{ $employeeName }}
- **Data:** {{ $day }}
- **Horário:** {{ $hour }}
- **Valor:** {{ $price }}
</x-mail::panel>

Qualquer dúvida, entre em contato conosco!

Obrigado,<br>
**Salão Fácil**
</x-mail::message>
