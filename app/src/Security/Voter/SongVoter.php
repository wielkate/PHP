<?php
/**
 * Song voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\Song;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Song voter.
 */
class SongVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * Edit permission.
     *
     * @const string
     */
    private const INDEX = 'INDEX';

    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * @param string $attribute param
     * @param mixed  $subject   param
     *
     * @return bool result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Song;
    }

    /**
     * @param string         $attribute param
     * @param mixed          $subject   param
     * @param TokenInterface $token     param
     *
     * @return bool result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (self::VIEW === $attribute) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView(),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * @param Song          $song param
     * @param UserInterface $user param
     *
     * @return bool result
     */
    private function canEdit(Song $song, UserInterface $user): bool
    {
        $roles = $user->getRoles();

        return in_array(UserRole::ROLE_ADMIN->value, $roles);
    }

    /**
     * @return bool result
     */
    private function canView(): bool
    {
        return true;
    }

    /**
     * @param Song          $song param
     * @param UserInterface $user param
     *
     * @return bool result
     */
    private function canDelete(Song $song, UserInterface $user): bool
    {
        $roles = $user->getRoles();

        return in_array(UserRole::ROLE_ADMIN->value, $roles);
    }
}
