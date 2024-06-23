# File Encryption and Decryption with Laravel

## Project Overview

This Laravel web application allows users to encrypt and decrypt files using OpenSSL. The application provides the following features:
- Select any file from the user's computer.
- Display file details (name, size, extension).
- Encrypt files using AES-256-CBC.
- Decrypt files to retrieve the original content.
- Save the encrypted/decrypted file with a user-defined name and location.

## Features

1. **File Selection and Details Display**:
    - Users can select any file from their computer.
    - The application displays file details including name, size, and extension.

2. **Encryption Process**:
    - Users can encrypt the selected file using the AES-256-CBC algorithm.
    - Users can specify the output file name and location for the encrypted file.

3. **Decryption Process**:
    - Users can decrypt an encrypted file to retrieve the original content.
    - Users can specify the output file name and location for the decrypted file.

4. **Save File Feature**:
    - Allows users to select the name and location for saving the encrypted/decrypted file.

## Code Structure

### Model
- `File` Model: Represents the file entity with attributes like path, name, size, and extension.

### Repository
- `FileRepository`: Implements file operations such as storing, retrieving, and deleting files.

### Use Cases
- `EncryptFileUseCase`: Encrypts the file content.
- `DecryptFileUseCase`: Decrypts the file content.
- `UploadFileUseCase`: Handles file uploads.
- `ValidateEncryptionUseCase`: Validates the encryption by comparing original and decrypted files.
- `FileUseCases`: Acts as a factory to get instances of use cases.

### Controller
- `FileController`: Handles HTTP requests related to file operations and invokes the appropriate use cases.

### Helpers
- `ErrorHelper`: Logs errors with different levels.
- `ErrorLevels`: Enum representing different error levels.

## Software Modularity

- **Reusable Components**:
  - Use cases are designed as singletons for easy reuse.
  - Repository pattern decouples data access logic.
- **Design Patterns**: Used SOLID principles to ensure modularity and reusability.

## Code Quality 

- **Clean Code**: Adhered to PSR standards.
