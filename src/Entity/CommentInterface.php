<?php

namespace Drupal\vaterno_comments\Entity;

interface CommentInterface
{
    public function label();
    public function getCommentText(): string;
    public function setCommentText(string $commentText): static;
    public function getCreatedTime(): \DateTime;
    public function setCreatedTime(int|\DateTime $time): static;
    public function getUserId(): int;
    public function setUserId(int $userId): static;
}
