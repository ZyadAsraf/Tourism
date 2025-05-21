# Tourism API Documentation

This document provides details about the API endpoints for the Tourism application, focusing on Tickets and Cart functionality.

## Authentication

All API endpoints require authentication unless stated otherwise.

- **Login**
  - POST `/api/login`
  - Body: `{ "email": "user@example.com", "password": "password" }`

- **Register**
  - POST `/api/register`
  - Body: `{ "name": "User Name", "email": "user@example.com", "password": "password", "password_confirmation": "password" }`

## Ticket Endpoints

### Get All Tickets

Retrieves all tickets for the authenticated user.

- **URL**: `/api/tickets`
- **Method**: GET
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "data": {
      "tickets": [
        {
          "id": 1,
          "title": "Attraction Name",
          "slug": "attraction-name",
          "price": 100,
          "rating": 4.5,
          "reviewCount": 120,
          "description": "Attraction description",
          "image": "/storage/images/attraction.jpg",
          "quantity": 2,
          "date": "2025-06-01",
          "time": "10:00 AM",
          "ticket_type": "Adult",
          "ticket_id": 1,
          "phone": "1234567890",
          "subtotal": 200,
          "booking_time": "2025-05-21 10:30:00",
          "state": "confirmed"
        }
      ],
      "total": 200
    }
  }
  ```

### Get Specific Ticket

Retrieves details for a specific ticket.

- **URL**: `/api/tickets/{id}`
- **Method**: GET
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "data": {
      "ticket": {
        "id": 1,
        "quantity": 2,
        "booking_time": "2025-05-21 10:30:00",
        "total_cost": 200,
        "visit_date": "2025-06-01",
        "time_slot": "10:00 AM",
        "phone_number": "1234567890",
        "state": "confirmed"
      },
      "attraction": {
        "id": 1,
        "title": "Attraction Name",
        "slug": "attraction-name",
        "price": 100,
        "rating": 4.5,
        "reviewCount": 120,
        "description": "Attraction description",
        "image": "/storage/images/attraction.jpg"
      },
      "ticket_type": {
        "id": 1,
        "title": "Adult",
        "description": "Standard adult ticket"
      },
      "review_stats": {
        "average_rating": 4.5,
        "review_count": 120
      }
    }
  }
  ```

### Get Ticket Types

Retrieves all available ticket types.

- **URL**: `/api/ticket-types`
- **Method**: GET
- **Authentication**: Not required
- **Response**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "Title": "Adult",
        "Description": "Standard adult ticket"
      },
      {
        "id": 2,
        "Title": "Child",
        "Description": "For children under 12"
      }
    ]
  }
  ```

## Cart Endpoints

### Get Cart Contents

Retrieves all items in the authenticated user's cart.

- **URL**: `/api/cart`
- **Method**: GET
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "data": {
      "attractions": [
        {
          "id": 1,
          "title": "Attraction Name",
          "slug": "attraction-name",
          "price": 100,
          "rating": 4.5,
          "reviewCount": 120,
          "description": "Attraction description",
          "image": "/storage/images/attraction.jpg",
          "quantity": 2,
          "date": "2025-06-01",
          "time": "10:00 AM",
          "ticket_type_id": 1,
          "cart_item_id": 1,
          "subtotal": 200
        }
      ],
      "total": 200
    }
  }
  ```

### Add to Cart

Adds an attraction to the cart.

- **URL**: `/api/cart/add`
- **Method**: POST
- **Authentication**: Required
- **Body**:
  ```json
  {
    "attraction_id": 1,
    "date": "2025-06-01",
    "time": "10:00 AM",
    "quantity": 2,
    "ticket_type_id": 1
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Attraction added to cart",
    "data": {
      "cart_item": {
        "id": 1,
        "user_id": 1,
        "attraction_id": 1,
        "ticket_type_id": 1,
        "quantity": 2,
        "date": "2025-06-01",
        "time": "10:00 AM",
        "created_at": "2025-05-21T10:30:00.000000Z",
        "updated_at": "2025-05-21T10:30:00.000000Z"
      }
    }
  }
  ```

### Update Cart Item

Updates the quantity of an item in the cart.

- **URL**: `/api/cart/{id}`
- **Method**: PUT
- **Authentication**: Required
- **Body**:
  ```json
  {
    "quantity": 3
    "date":2025-06-01
    "time":"morning"
    "ticket_type_id":1
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Cart updated",
    "data": {
      "cart_item": {
        "id": 1,
        "user_id": 1,
        "attraction_id": 1,
        "ticket_type_id": 1,
        "quantity": 3,
        "date": "2025-06-01",
        "time": "morning",
        "created_at": "2025-05-21T10:30:00.000000Z",
        "updated_at": "2025-05-21T10:35:00.000000Z"
      }
    }
  }
  ```

### Remove from Cart

Removes an item from the cart.

- **URL**: `/api/cart/{id}`
- **Method**: DELETE
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "message": "Item removed from cart"
  }
  ```

### Clear Cart

Removes all items from the cart.

- **URL**: `/api/cart`
- **Method**: DELETE
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "message": "Cart has been cleared"
  }
  ```

### Checkout

Processes the checkout and converts cart items to tickets.

- **URL**: `/api/cart/checkout`
- **Method**: POST
- **Authentication**: Required
- **Body**:
  ```json
  {
    "PhoneNumber": "1234567890",
    "state": "confirmed"
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Checkout successful",
    "data": {
      "tickets_created": 1,
      "tickets": [
        {
          "TouristId": 1,
          "Attraction": 1,
          "TicketTypesId": 1,
          "Quantity": 2,
          "BookingTime": "2025-05-21T10:40:00.000000Z",
          "TotalCost": 200,
          "VisitDate": "2025-06-01",
          "TimeSlot": "10:00 AM",
          "PhoneNumber": "1234567890",
          "state": "confirmed",
          "id": 1
        }
      ]
    }
  }
  ```

### Get Cart Count

Returns the number of items in the cart.

- **URL**: `/api/cart/count`
- **Method**: GET
- **Authentication**: Required
- **Response**:
  ```json
  {
    "success": true,
    "data": {
      "count": 1
    }
  }
  ```
