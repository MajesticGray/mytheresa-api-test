# Mytheresa API Test

This repository contains a functional RESTful API to interact with the backend, as well as multiple tests to ensure everything is working correctly.  
The necessary scripts to build and run this project are also included.

## Overview

Mytheresa API Test is a PHP project built using **Symfony** and **API Platform**, a powerful combination for building modern APIs that provides:

- **Rapid Development:** Easy REST & GraphQL endpoint generation.
- **Built-in Features:** Pagination, filtering, validation, serialization, and documentation.
- **Flexibility:** Works with custom DTOs, services, and external data sources.
- **Security:** Supports JWT authentication, OAuth, and role-based access control.
- **Scalability:** Optimized for high-performance APIs with caching, Mercure for real-time updates, and GraphQL support.

These advantages make this stack ideal for **RESTful APIs, GraphQL APIs, microservices, and headless applications**.

The source code is structured following **DDD best practices and recommendations**.

For additional architectural details and explanations on decisions taken, refer to the [architecture.md](architecture.md) file.

---

## **Project Setup**

### [docker-compose.yml](docker-compose.yml)
This file contains the instructions to set up the full environment, including a web server and a database server.  
Once running, the project should be accessible by default at [http://localhost:3000](http://localhost:3000).

### [Dockerfile](Dockerfile)
This file contains the instructions to build a PHP web server image, based on the official image published on Docker Hub:  
[php:8.4-apache](https://hub.docker.com/layers/library/php/8.4-apache/images/sha256-f722d3f411b2951405044dfe1c6a7ffd2bbd8662f4b7cfd7ab162974767a38a4).  
It also includes the required PHP extensions and Composer for installing dependencies.

---

## **Installation**

1. **Clone the repository**
   ```sh
   git clone https://github.com/MajesticGray/mytheresa-api-test.git
   cd mytheresa-api-test
   ```
   
   This project is preconfigured to use **port 3000**.  
   If you want to change it, modify the `API_EXPOSED_PORT` variable at the end of the [`.env`](.env#L35) file.

2. **Ensure Docker Compose is installed**
   ```sh
   docker compose version
   ```
   You should see something like:
   ```
   Docker Compose version v2.32.4
   ```

3. **Start the Docker environment**
   ```sh
   docker compose up -d
   ```

   This will pull and build the required resources from the internet.  It may take a while to build, so please be patient.  
   Once finished, two containers will be up and running:

   - `mytheresa_backend_php`
   - `mytheresa_backend_mysql`

   as well as a volume for database storage:

   - `<folder>_mytheresa_db`

   where `<folder>` is the directory where the project was cloned (`mytheresa-api-test` by default).

4. **Install PHP dependencies**
   ```sh
   docker exec -it mytheresa_backend_php composer install
   ```

You're done! If you kept the default port unchanged, you can access the project homepage at  
[http://localhost:3000](http://localhost:3000).

---

## **Testing**

You can run the tests provided with the following command:

```sh
docker exec -it mytheresa_backend_php bin/phpunit
```

To run only the tests from a specific group (`api` or `domain`):
```sh
docker exec -it mytheresa_backend_php bin/phpunit --group=api
```

> **NOTE:** if running `bin/phpunit` raises the message **'There is already an active transaction'**, please, run it again and it will work.  
> This may happen only the first time the tests are run, and it is caused by how Foundry resets and initialises the test database.
> I am currently looking into this issue.

---

## **Using the API**

### **Homepage Placeholder**
[http://localhost:3000](http://localhost:3000)  
This is Symfony's default homepage placeholder.  
It has been left unchanged to test the framework installation.  
If there are missing dependencies or other issues, they will appear here.

### **API Format**
The API utilizes **JSON format** for data exposure.  
More information about the JSON:API standard can be found at [jsonapi.org](https://jsonapi.org/).  
It is a simpler alternative to `application/ld+json`, but still provides meaningful metadata along with the data.

---

### **Available Endpoints**

#### **Products Endpoint**
[http://localhost:3000/products](http://localhost:3000/products)  
This is the **main and only exposed endpoint**. It returns all products from the database.  
The response is in `application/vnd.api+json` format.

#### **Filters**

##### **Filter by category (`boots`)**
[http://localhost:3000/products?category=boots](http://localhost:3000/products?category=boots)

##### **Filter by category (`boots`) and price lower than 71000**
[http://localhost:3000/products?category=boots&priceLessThan=71000](http://localhost:3000/products?category=boots&priceLessThan=71000)

---

### **Pagination**
The results returned by the API are **paginated (5 items per page by default)**.  
To customize the pagination size, use the `itemsPerPage` parameter:
[http://localhost:3000/products?category=boots&itemsPerPage=1](http://localhost:3000/products?category=boots&itemsPerPage=1)

Check the `links` and `meta` attributes included in the API response to navigate through the paginated results.

---

## **Tech details and decision Rationale**

For additional architectural details and explanations on decisions taken, refer to the [architecture.md](architecture.md) file.

## **Need Help? Want to Get in Touch?**
Feel free to reach out via email:  
📧 [hector.rovira@me.com](mailto:hector.rovira@me.com)
