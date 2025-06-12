<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperación de contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 2rem; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: white; margin: 0; font-size: 1.5rem;">Rayito de Sol</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 2rem; border-radius: 0 0 8px 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #1e293b; margin-bottom: 1rem;">Hola {{ $user->name }},</h2>
            
            <p style="color: #64748b; line-height: 1.6; margin-bottom: 1.5rem;">
                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. 
                Si no realizaste esta solicitud, puedes ignorar este correo.
            </p>
            
            <p style="color: #64748b; line-height: 1.6; margin-bottom: 2rem;">
                Para restablecer tu contraseña, haz clic en el siguiente enlace:
            </p>
            
            <!-- Button -->
            <div style="text-align: center; margin: 2rem 0;">
                <a href="{{ $resetUrl }}" 
                   style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); 
                          color: white; 
                          padding: 1rem 2rem; 
                          text-decoration: none; 
                          border-radius: 8px; 
                          font-weight: 600;
                          display: inline-block;">
                    Restablecer contraseña
                </a>
            </div>
            
            <p style="color: #64748b; font-size: 0.875rem; line-height: 1.6;">
                Este enlace expirará en 1 hora por seguridad. Si el enlace no funciona, 
                copia y pega la siguiente URL en tu navegador:
            </p>
            
            <p style="color: #3b82f6; font-size: 0.875rem; word-break: break-all; 
                      background: #f8fafc; padding: 0.75rem; border-radius: 4px; margin: 1rem 0;">
                {{ $resetUrl }}
            </p>
            
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 2rem 0;">
            
            <p style="color: #94a3b8; font-size: 0.75rem; text-align: center;">
                Si tienes problemas, contacta nuestro soporte técnico.
            </p>
        </div>
    </div>
</body>
</html>
