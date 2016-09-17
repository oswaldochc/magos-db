<?php
namespace Magos\Db\TableGateway\Feature;

class SequenceFeature extends \Zend\Db\TableGateway\Feature\SequenceFeature
{
    /**
     * @var string
     */
    protected $primaryKeyField;

    /**
     * @var string
     */
    protected $sequenceName;

    /**
     * @var int
     */
    protected $sequenceValue;


    /**
     * @param string $primaryKeyField
     * @param string $sequenceName
     */
    public function __construct($primaryKeyField, $sequenceName)
    {
        $this->primaryKeyField = $primaryKeyField;
        $this->sequenceName    = $sequenceName;
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * @return int
     */
    public function nextSequenceId()
    {
        $platform = $this->tableGateway->adapter->getPlatform();
        $platformName = $platform->getName();

        switch ($platformName) {
            case 'Oracle':
                $sql = 'SELECT ' . $platform->quoteIdentifier($this->sequenceName) . '.NEXTVAL as "nextval" FROM dual';
                break;
            case 'PostgreSQL':
                $sql = 'SELECT NEXTVAL(\'' . $this->sequenceName . '\')';
                break;
            default :
                return;
        }

        $statement = $this->tableGateway->adapter->createStatement();
        $statement->prepare($sql);
        $result = $statement->execute();
        $sequence = $result->current();
        unset($statement, $result);
        return $sequence['nextval'];
    }
}
