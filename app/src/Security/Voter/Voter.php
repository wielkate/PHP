<?php
/**
 * Song voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\Song;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SongVoter.
 */
class SongVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * View permission.
     *
     * @const string
     */
    public const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * OrderVoter constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Song;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit Song.
     *
     * @param Song $song Song entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canEdit(Song $song, User $user): bool
    {
        return in_array(UserRole::ROLE_ADMIN->value | UserRole::ROLE_USER->value, $user->getRoles());
    }

    /**
     * Checks if user can view Song.
     *
     * @param Song $song Song entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canView(Song $song, User $user): bool
    {
        return in_array(UserRole::ROLE_ADMIN->value | UserRole::ROLE_USER->value, $user->getRoles());
    }

    /**
     * Checks if user can delete Song.
     *
     * @param Song $song Song entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canDelete(Song $song, User $user): bool
    {
        return in_array(UserRole::ROLE_ADMIN->value | UserRole::ROLE_USER->value, $user->getRoles());
    }
}
