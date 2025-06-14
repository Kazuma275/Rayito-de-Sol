<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contraseña restablecida</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 2rem; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: white; margin: 0; font-size: 1.5rem;">Rayito de Sol</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 2rem; border-radius: 0 0 8px 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #1e293b; margin-bottom: 1rem;">¡Hola {{ $user->name }}!</h2>
            
            <h3 style="color: #10b981; margin-bottom: 1rem;">¡Contraseña restablecida!</h3>
            
            <p style="color: #64748b; line-height: 1.6; margin-bottom: 1.5rem;">
                Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión con tu nueva contraseña.
            </p>
            
            <p style="color: #64748b; line-height: 1.6; margin-bottom: 2rem;">
                Si no realizaste este cambio, contacta inmediatamente nuestro soporte técnico.
            </p>
            
            <!-- Button -->
            <div style="text-align: center; margin: 2rem 0;">
                <a href="{{ env('FRONTEND_URL') }}/login" 
                   style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); 
                          color: white; 
                          padding: 1rem 2rem; 
                          text-decoration: none; 
                          border-radius: 8px; 
                          font-weight: 600;
                          display: inline-block;">
                    Iniciar sesión
                </a>
            </div>
            
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 2rem 0;">
            
            <p style="color: #94a3b8; font-size: 0.75rem; text-align: center;">
                Este es un mensaje automático, por favor no respondas a este correo.
            </p>
        </div>
    </div>
</body>
</html>
