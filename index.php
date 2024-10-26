<?php

use PDO;

class OrderService
{
    private PDO $pdo;
    private string $apiUrl;

    public function __construct(PDO $pdo, string $apiUrl)
    {
        $this->pdo = $pdo;
        $this->apiUrl = $apiUrl;
    }

    public function createOrder(int $eventId, string $eventDate, int $ticketAdultPrice, int $ticketAdultQuantity, int $ticketKidPrice, int $ticketKidQuantity): void
    {
        $barcode = $this->generateBarcode();
        $equalPrice = $ticketAdultPrice * $ticketAdultQuantity + $ticketKidPrice * $ticketKidQuantity;

        $response = $this->bookOrder($eventId, $eventDate, $ticketAdultPrice, $ticketAdultQuantity, $ticketKidPrice, $ticketKidQuantity, $barcode);

        if ($response['error'] === 'barcode already exists') {
            $this->createOrder($eventId, $eventDate, $ticketAdultPrice, $ticketAdultQuantity, $ticketKidPrice, $ticketKidQuantity);
        } else {
            $this->approveOrder($barcode);
            $this->saveOrder($eventId, $eventDate, $ticketAdultPrice, $ticketAdultQuantity, $ticketKidPrice, $ticketKidQuantity, $barcode, $equalPrice);
        }
    }

    private function generateBarcode(): string
    {
        return str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    private function bookOrder(int $eventId, string $eventDate, int $ticketAdultPrice, int $ticketAdultQuantity, int $ticketKidPrice, int $ticketKidQuantity, string $barcode): array
    {
        $curl = curl_init($this->apiUrl . '/book');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
            'event_id' => $eventId,
            'event_date' => $eventDate,
            'ticket_adult_price' => $ticketAdultPrice,
            'ticket_adult_quantity' => $ticketAdultQuantity,
            'ticket_kid_price' => $ticketKidPrice,
            'ticket_kid_quantity' => $ticketKidQuantity,
            'barcode' => $barcode,
        ]));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    private function approveOrder(string $barcode): void
    {
        $curl = curl_init($this->apiUrl . '/approve');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
            'barcode' => $barcode,
        ]));

        $response = curl_exec($curl);
        curl_close($curl);

        if (json_decode($response, true)['error']) {
            throw new Exception('Order approval failed');
        }
    }

    private function saveOrder(int $eventId, string $eventDate, int $ticketAdultPrice, int $ticketAdultQuantity, int $ticketKidPrice, int $ticketKidQuantity, string $barcode, int $equalPrice): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO orders (event_id, event_date, ticket_adult_price, ticket_adult_quantity, ticket_kid_price, ticket_kid_quantity, barcode, equal_price) VALUES (:event_id, :event_date, :ticket_adult_price, :ticket_adult_quantity, :ticket_kid_price, :ticket_kid_quantity, :barcode, :equal_price)');
        $stmt->execute([
            'event_id' => $eventId,
            'event_date' => $eventDate,
            'ticket_adult_price' => $ticketAdultPrice,
            'ticket_adult_quantity' => $ticketAdultQuantity,
            'ticket_kid_price' => $ticketKidPrice,
            'ticket_kid_quantity' => $ticketKidQuantity,
            'barcode' => $barcode,
            'equal_price' => $equalPrice,
        ]);
    }
}