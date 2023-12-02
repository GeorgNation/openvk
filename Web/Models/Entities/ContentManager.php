<?php declare(strict_types=1);
namespace openvk\Web\Models\Entities;
use openvk\Web\Util\DateTime;
use openvk\Web\Models\RowModel;
use openvk\Web\Models\Entities\{ContentOwner, User};
use openvk\Web\Models\Repositories\{Users, ContentOwners};
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;
use Chandler\Security\User as ChandlerUser;

class ContentManager extends RowModel
{
    protected $tableName = "cms_managers";
    
    function getId(): int
    {
        return $this->getRecord()->id;
    }
        
    function getUserId(): int
    {
        return $this->getRecord()->user;
    }

    function getUser(): ?User
    {
        return (new Users)->get($this->getRecord()->user);
    }

    function getCmsId(): int
    {
        return $this->getRecord()->cms;
    }

    function getCms(): ?ContentOwner
    {
        return (new ContentOwners)->get($this->getRecord()->cms);
    }
        
    use Traits\TSubscribable;
}
