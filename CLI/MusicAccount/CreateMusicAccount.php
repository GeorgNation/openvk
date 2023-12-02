<?php declare(strict_types=1);
namespace openvk\CLI\MusicAccount;
use Chandler\Database\DatabaseConnection;
use Chandler\Session\Session;
use Chandler\Security\User as ChandlerUser;
use Chandler\Security\Authenticator;
use openvk\Web\Models\Repositories\Photos;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Nette\Utils\ImageException;

class CreateMusicAccount extends Command
{
    private $images;

    protected static $defaultName = "music-accounts:create-user";

    function __construct()
    {
        $this->images = DatabaseConnection::i()->getContext()->table("photos");

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("Create music account user")
            ->setHelp("This command allows you to create a music account to store music assets uploaded through the CMS");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $header  = $output->section();
        $counter = $output->section();

        $header->writeln([
            "Music Account creation wizard",
            "=====================",
            "",
            "Please take a note, that all Music Accounts on OpenVK will be automatically verified, and special alert will be present on the user page.",
            "After the successfully creation of the Music Account, this command will output the ID of your account.",
            "Please take a note, that for the Music Accounts emails will be allocated automatically, wherefore the \"Forgot password\" function will not work for these accounts because of non-sense."
        ]);

        $accountNameQuestion = new Question ('Enter your new music account first name (default: Музыка): ', 'Музыка');
        $accountLastNameQuestion = new Question ('Enter your new music account last name (default: OpenVK): ', 'OpenVK');

        $accountEmail = "music-" . bin2hex (random_bytes (8)) . "@localhost.localdomain";
        $accountPassword = sha1 (random_bytes (16));

        $user = new User;
        $user->setFirst_Name($accountNameQuestion);
        $user->setLast_Name($accountLastNameQuestion);
        $user->setSex(2);
        $user->setEmail($accountEmail);
        $user->setSince(date("Y-m-d H:i:s"));
        $user->setRegistering_Ip("127.0.0.1");
        $user->setBirthday(NULL);
        $user->setActivated(1);
        $user->setVerified(1);
        $user->setType(USER::TYPE_MUSIC); # Automatically generated account
        $user->setAlert("This account is automatically created by OpenVK Content Management System.");
        $chUser = ChandlerUser::create($accountEmail, $accountPassword);
        $user->setUser($chUser->getId());

        $user->setPrivacySetting ("page.read", User::PRIVACY_EVERYONE);
        $user->setPrivacySetting ("page.info.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("groups.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("photos.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("videos.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("notes.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("friends.read", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("friends.add", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("wall.write", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("messages.write", User::PRIVACY_NO_ONE);
        $user->setPrivacySetting ("audios.read", User::PRIVACY_ONLY_REGISTERED);

        $user->save(false);

        $output->writeln ([
            "",
            "Your Music Account is successfully created.",
            "",
            "Account ID: " . $user->getId (),
            ""
        ]);

        return Command::SUCCESS;
    }
}