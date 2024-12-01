<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Development Mode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }
        .error-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error-type {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .error-context {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .context-item {
            margin-bottom: 5px;
        }
        .stack-trace {
            font-family: monospace;
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-type"><?php echo htmlspecialchars($type); ?></h1>
        
        <div class="error-message">
            <?php echo htmlspecialchars($message); ?>
        </div>
        
        <div class="error-context">
            <div class="context-item">
                <strong>File:</strong> <?php echo htmlspecialchars($context['file']); ?>
            </div>
            <div class="context-item">
                <strong>Line:</strong> <?php echo htmlspecialchars($context['line']); ?>
            </div>
            <?php if (isset($context['type'])): ?>
            <div class="context-item">
                <strong>Type:</strong> <?php echo htmlspecialchars($context['type']); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (isset($context['trace'])): ?>
        <div class="stack-trace">
            <strong>Stack Trace:</strong>
            <?php echo htmlspecialchars($context['trace']); ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
