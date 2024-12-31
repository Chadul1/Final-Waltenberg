<?php
namespace Logger;

//phpinfo();

/**
 * Simulating an Enum for log levels (PHP 8.0)
 * This defines the available logging levels in order of increasing severity.
 */
class LogLevel
{
    const DEBUG = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;
    
    // Optional: Adding a method to get the name of the log level
    public static function getName(int $level): string
    {
        switch ($level) {
            case self::DEBUG:
                return 'DEBUG';
            case self::INFO:
                return 'INFO';
            case self::WARNING:
                return 'WARNING';
            case self::ERROR:
                return 'ERROR';
            default:
                throw new \InvalidArgumentException("Invalid log level: $level");
        }
    }
}

/**
 * Logger interface
 * This interface defines the contract that all logger implementations must follow.
 */
interface LoggerInterface
{
    public function debug(string $message): void;
    public function info(string $message): void;
    public function warning(string $message): void;
    public function error(string $message): void;
    public function setLogLevel(int $level): void;
}

/**
 * Abstract logger class
 * This class provides a base implementation for all loggers.
 * It implements common functionality and leaves specific logging behavior to child classes.
 */
abstract class AbstractLogger implements LoggerInterface
{
    protected int $logLevel;

    public function __construct(int $logLevel = LogLevel::INFO)
    {
        $this->logLevel = $logLevel;
    }

    public function setLogLevel(int $level): void
    {
        $this->logLevel = $level;
    }

    /**
     * Determines if a message should be logged based on its level and the current log level.
     */
    protected function shouldLog(int $messageLevel): bool
    {
        return $messageLevel >= $this->logLevel;
    }

    /**
     * Formats the log message with a timestamp and log level.
     */
    protected function formatMessage(int $level, string $message): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $levelName = LogLevel::getName($level);
        return "[$timestamp] {$levelName}: $message";
    }

    /**
     * Abstract method to be implemented by child classes for actual logging.
     */
    abstract protected function writeLog(string $formattedMessage): void;

    // Implementation of LoggerInterface methods
    public function debug(string $message): void
    {
        if ($this->shouldLog(LogLevel::DEBUG)) {
            $this->writeLog($this->formatMessage(LogLevel::DEBUG, $message));
        }
    }

    public function info(string $message): void
    {
        if ($this->shouldLog(LogLevel::INFO)) {
            $this->writeLog($this->formatMessage(LogLevel::INFO, $message));
        }
    }
    
    public function warning(string $message): void
    {
        if ($this->shouldLog(LogLevel::WARNING)) {
            $this->writeLog($this->formatMessage(LogLevel::WARNING, $message));
        }
    }

    public function error(string $message): void
    {
        if ($this->shouldLog(LogLevel::ERROR)) {
            $this->writeLog($this->formatMessage(LogLevel::ERROR, $message));
        }
    }
}

/**
 * Text logger implementation
 * This class writes log messages to a text file.
 */
class TextLogger extends AbstractLogger
{
    private string $filePath;

    public function __construct(string $filePath, int $logLevel = LogLevel::INFO)
    {
        parent::__construct($logLevel);
        $this->filePath = $filePath;
    }

    protected function writeLog(string $formattedMessage): void
    {
        try {
            // Define the path to the log file
            $logDirectory = __DIR__ . '/log';
            $logFilePath = $logDirectory . '/file.log';

            // Check if the directory exists, if not, create it
            if (!is_dir($logDirectory)) {
                mkdir($logDirectory, 0777, true);
            }

            // Write the log message to the file
            file_put_contents($logFilePath, $formattedMessage . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            // Handle the error gracefully
            error_log("Failed to write to log file: " . $e->getMessage());
        }
    }    
}

/**
 * Database logger implementation
 * This class writes log messages to a database table.
 */
class DatabaseLogger extends AbstractLogger
{
    private \PDO $pdo;
    private string $tableName;

    public function __construct(\PDO $pdo, string $tableName, int $logLevel = LogLevel::INFO)
    {
        parent::__construct($logLevel);
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    //I didn't really know how this stuff worked and I couldn't find the recording to go over it again. 
    protected function writeLog(string $formattedMessage): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->tableName} (message) VALUES (:message)");
            $stmt->execute(['message' => $formattedMessage]);
        } catch (\PDOException $e) {
            // In a real-world scenario, you might want to handle this error more gracefully
            error_log("Failed to write to database log: " . $e->getMessage());
        }
    }
}

/**
 * Logger factory (Singleton)
 * This class ensures that only one instance of the logger is created and used throughout the application.
 * It implements the Singleton pattern, which is crucial for maintaining a single point of control for logging.
 */
class LoggerFactory
{
    // The single instance of this class
    private static ?LoggerFactory $instance = null;
    
    private LoggerInterface $logger ;

    // Private constructor to prevent direct creation of object
    private function __construct() {}

    /**
     * Get the single instance of LoggerFactory
     * If the instance doesn't exist, it creates one
     */
    public static function getInstance(): LoggerFactory
    {
        if (self::$instance === null) {
            self::$instance = new LoggerFactory();
        }
        return self::$instance;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}

/**
 * Initialize the logger
 * This function sets up the logger based on the specified type.
 */
function initializeLogger(string $type = 'database'): void
{
    $factory = LoggerFactory::getInstance();
    
    try {
        //Creates a text logger in the case that the type is == to text. Same goes for the database logger.
        if ($type === 'text') {
            $logFilePath = __DIR__ . 'log/file.log';
            $logger = new TextLogger($logFilePath, LogLevel::DEBUG);  
        } elseif ($type === 'database') {
            $pdo = new \PDO('mysql:host=localhost;dbname=logger_db', 'root', '');
            $logger = new DatabaseLogger($pdo, 'logs', LogLevel::INFO);
        } else {
            throw new \InvalidArgumentException("Invalid logger type: $type");
        }
        
        $factory->setLogger($logger);
    } catch (\Exception $e) {
        // Handles errors.
        error_log("Failed to initialize logger: " . $e->getMessage() . "Created Text logger instead.");
        // Set a default to a text document incase of error when logger is created. 
        $logFilePath = __DIR__ . 'log/file.log';
        $logger = new TextLogger($logFilePath, LogLevel::DEBUG);
        $factory->setLogger($logger);
    }
}



